<?php

namespace api\controllers;

use common\components\MainFunctions;
use common\components\ZhkhActiveRecord;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\House;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Organization;
use common\models\Request;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Settings;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class IntegrationIsController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return array_merge(
            $behaviors,
            ['verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'notify' => ['post'],
                ],
            ],
                'contentNegotiator' => [
                    'class' => ContentNegotiator::class,
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON
                    ]
                ],
            ]
        );

    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = [
            'notify',
        ];
        return $actions;
    }

    /**
     * @param $orgUuid
     * @return array
     * @throws BadRequestHttpException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionNotify($orgUuid)
    {
        $request = Yii::$app->request;
        file_put_contents(Yii::getAlias('@api/runtime/logs/is-' . date('Ymd-His') . '.log'),
            json_encode($request->getBodyParams()));

        $apiIp = Settings::findOne(['uuid' => Settings::SETTING_IS_IP])->parameter;
        if (!strstr($apiIp, $request->remoteIP)) {
            throw new BadRequestHttpException();
        }

        $organisation = Organization::findOne(['uuid' => $orgUuid]);
        if ($organisation == null) {
            throw new BadRequestHttpException();
        }

        $operation = $request->getBodyParam('operation');
        if ($operation != 'create') {
            throw new BadRequestHttpException();
        }

        $object = $request->getBodyParam('object');
        if ($object != 'appeal') {
            throw new BadRequestHttpException();
        }

        $data = $request->getBodyParam('data');

        // ищем дом по gis_id
        $fiasHouseUuid = strtoupper($data['address']['buildingFiasId']);
        $house = House::find()->where(['gis_id' => $fiasHouseUuid])->one();
        if ($house == null) {
            // TODO: что-то нужно сделать. Может получится так что дом закрепили за ДСС а они его еще в базу не добавили
            // либо по какой-то причине изменился uuid
            throw new \Exception('Интерсвязь прислала обращение, с uuid дома которого нет в базе.');
        }

        // ищем объект по квартире или "Общий" если квартира не указана
        $localObject = null;
        $flatNum = 0;
        if (isset($data['address']['flatNum'])) {
            $flatNum = $data['address']['flatNum'];
        }

        if ($flatNum > 0) {
            $localObject = Objects::find()->where(['title' => $flatNum, 'houseUuid' => $house->uuid])->one();
        } else {
            $localObject = Objects::find()->where([
                'title' => Objects::COMMON_OBJECT_TITLE,
                'houseUuid' => $house->uuid,
                'objectTypeUuid' => ObjectType::OBJECT_TYPE_GENERAL
            ])->one();
        }

        $localObjectUuid = $localObject != null ? $localObject->uuid : null;
        // TODO: нужно как-то уведомить админа что не нашли дом или "Общий" объект

        foreach ($data['incidents'] as $incident) {
            // ищем контрагента по extId, если не находим создаём его
            $contrUuid = null;
            $contrExtId = $flatNum = $incident['regUser']['id'];
            $contr = Contragent::find()->where(['extId' => $contrExtId, 'oid' => $orgUuid])->one();
            if ($contr != null) {
                $contrUuid = $contr->uuid;
            } else {
                $contr = new Contragent();
                $contr->uuid = MainFunctions::GUID();
                $contr->oid = $orgUuid;
                $contr->title = $incident['regUser']['fullName'];
                $contr->address = $incident['address']['text'];
                $contr->phone = '' . $incident['phone'];
                $contr->email = $incident['email'];
                $contr->extId = $incident['regUser']['id'];
                $contr->contragentTypeUuid = ContragentType::CITIZEN;
                if (!$contr->save()) {
                    // TODO: нужно как-то уведомить админа что что-то не сохранилось
                } else {
                    $contrUuid = $contr->uuid;
                }
            }

            $request = new Request();
            $request->scenario = ZhkhActiveRecord::SCENARIO_API;
            $request->oid = $orgUuid;
            $request->uuid = MainFunctions::GUID();
            $request->type = 0; // 0 - бесплатная заявка (может можно сечь по категориям интерсвязи для точного определения)
            $request->contragentUuid = $contrUuid;
            $request->authorUuid = Users::USER_SERVICE_UUID; // sUser
            $request->requestStatusUuid = RequestStatus::NEW_REQUEST; // Состояние (1 - новое, 2 - в работе, 3 - закрыто)
            $requestType = RequestType::findOne(['title' => 'Другой характер обращения', 'oid' => $orgUuid])->uuid;
            $request->requestTypeUuid = $requestType;
            $request->comment = $data['category']['text'] . ': ' . $incident['description'];
            $request->verdict = '';
            $request->result = '';
            $request->equipmentUuid = null;
            $request->objectUuid = $localObjectUuid;
            $request->extId = $data['id']; // TODO: проверить! возможно это $incident['id'];
            $request->integrationClass = self::class;
//            $request->taskUuid; // null
            $request->closeDate = null; // ? current_timestamp можно ли установить в null?
            if (!$request->save()) {
                // TODO: нужно как-то уведомить админа что что-то не сохранилось
                return $request->errors;
            }
        }

        return [];
    }
}
