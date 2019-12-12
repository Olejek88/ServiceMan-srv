<?php

namespace api\controllers;

use common\components\MainFunctions;
use common\components\ZhkhActiveRecord;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\House;
use common\models\ObjectContragent;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Organization;
use common\models\OrganizationSub;
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
use yii\web\Response;

class IntegrationIsController extends Controller
{
    public const IS_API_PARAM_NAME = 'IS_API';
    public const IS_API_SECRET_NAME = 'IS_API_SECRET';

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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionNotify($orgUuid)
    {
        $request = Yii::$app->request;
        $headers = $request->getHeaders();
        $signature = $headers->get('X-Signature');
        $rawBody = $request->getRawBody();
        file_put_contents(Yii::getAlias('@api/runtime/logs/is-' . date('Ymd-His') . '.log'),
            $rawBody . PHP_EOL . '"X-Signature: ' . $signature . '"');
        $secret = self::getOrgSetting($orgUuid, self::IS_API_SECRET_NAME);
        $testSignature = hash_hmac('sha256', $rawBody, $secret->parameter);
        if ($testSignature !== $signature) {
            return [];
        }

        $organisation = Organization::findOne(['uuid' => $orgUuid]);
        if ($organisation == null) {
            // TODO: протоколирование уведомление о неизвестном uuid организации переданном интерсвязью
//            throw new BadRequestHttpException();
            return [];
        }

        $IS_API = self::getOrgSetting($orgUuid, self::IS_API_PARAM_NAME);
        $IS_API = json_decode($IS_API, true);

        $operation = $request->getBodyParam('operation');
        if (!in_array($operation, ['create', 'update'])) {
            // TODO: протоколирование уведомление о неизвестной операции
            return [];
        }

        $object = $request->getBodyParam('object');
        if (!in_array($object, ['appeal', 'comment', 'attachment'])) {
            // TODO: протоколирование уведомление о неизвестном объекте
            return [];
        }

        // достаём данные
        $data = $request->getBodyParam('data');

        switch ($operation) {
            case 'create' :
                switch ($object) {
                    case 'appeal' :
                        if (!self::createAppeal($orgUuid, $data)) {
                            // TODO: протоколирование уведомление об ошибке
                        }

                        return [];
                        break;
                    case 'comment' :
                        // TODO: реализовать обработку объекта comment
                        // как сохранить комментарий не ясно, т.к. в уведомлении кроме самого комментария ни чего больше нет
                        // т.е. мы его даже связать ни с одним обращением не можем.
                        return [];
                        break;
                    case 'attachment' :
                        // TODO: реализовать обработку объекта attachment
                        return [];
                        break;
                }
                break;
            case 'update' :
                // TODO: реализовать обработку операции update
                switch ($object) {
                    case 'appeal' :
                        if (!self::updateAppeal($data)) {
                            // TODO: протоколирование уведомление об ошибке
                        }

                        return [];
                        break;
                    case 'comment' :
                        // TODO: реализовать обработку объекта comment (возможно комментарии не меняются вовсе)
                        return [];
                        break;
                    case 'attachment' :
                        // TODO: реализовать обработку объекта attachment (возможно вложения не меняются вовсе)
                        return [];
                        break;
                }
        }

        return [];
    }

    /**
     * @param $oid string uuid организации
     * @param $data array массив с инфорацией об обращении
     * @return bool
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    private function createAppeal($oid, $data)
    {
        $oids = [$oid];
        // получаем все связанные организации
        $orgSubs = OrganizationSub::find()->where(['masterUuid' => $oid])->all();
        foreach ($orgSubs as $orgSub) {
            $oids[] = $orgSub->subUuid;
        }

        // ищем дом по gis_id, только для uuid организации для кторой выполняется web-hook и связанных с ней.
        $fiasHouseUuid = strtoupper($data['address']['buildingFiasId']);
        $house = House::find()->where([
            'gis_id' => $fiasHouseUuid,
            'oid' => $oids,
        ])->one();
        if ($house == null) {
            // TODO: что-то нужно сделать. Может получится так что дом закрепили за ДСС а они его еще в базу не добавили
            // либо по какой-то причине изменился uuid
//            throw new \Exception('Интерсвязь прислала обращение, с uuid дома которого нет в базе.');
            return false;
        }

        // uuid организации за которой закреплён дом, т.е. именно для неё будем создавать обращение
        $realOid = $house->oid;

        // ищем объект по квартире или "Общий" если квартира не указана
        $localObject = null;
        $flatNum = 0;
        if (isset($data['address']['flatNum'])) {
            $flatNum = $data['address']['flatNum'];
        }

        $isCommonObject = false;
        if ($flatNum > 0) {
            $localObject = Objects::find()->where(['title' => $flatNum, 'houseUuid' => $house->uuid])->one();
        } else {
            $localObject = Objects::find()->where([
                'title' => Objects::COMMON_OBJECT_TITLE,
                'houseUuid' => $house->uuid,
                'objectTypeUuid' => ObjectType::OBJECT_TYPE_GENERAL
            ])->one();
            $isCommonObject = true;
        }

        $localObjectUuid = $localObject != null ? $localObject->uuid : null;
        // TODO: нужно как-то уведомить админа что не нашли дом или "Общий" объект

        foreach ($data['incidents'] as $incident) {
            // сначала ищем связь по object_contragent
            $objectContr = ObjectContragent::find()->where(['objectUuid' => $localObjectUuid])->one();
            $contrUuid = null;
            if ($objectContr == null) {
                // если не нашли связи между объектом и контрагентом, считаем что контрагента нет, заводим нового
                $contr = new Contragent();
                $contr->uuid = MainFunctions::GUID();
                $contr->oid = $realOid;
                $contr->title = $incident['user']['fullName'];
                $contr->address = $incident['address']['text'];
                $contr->phone = '' . $incident['phone'];
                $contr->email = $incident['email'];
                $contr->extId = $incident['user']['id'];
                $contr->contragentTypeUuid = ContragentType::CITIZEN;
                if (!$contr->save()) {
                    // TODO: нужно как-то уведомить админа что что-то не сохранилось
                    return false;
                } else {
                    $contrUuid = $contr->uuid;
                    // создаём связь между объектом и контрагентом только для квартир, для общих объектов нет
                    if ($localObjectUuid != null && !$isCommonObject) {
                        $newObjContr = new ObjectContragent();
                        $newObjContr->uuid = MainFunctions::GUID();
                        $newObjContr->oid = $realOid;
                        $newObjContr->objectUuid = $localObjectUuid;
                        $newObjContr->contragentUuid = $contrUuid;
                        if (!$newObjContr->save()) {
                            // TODO: нужно как-то уведомить админа что что-то не сохранилось
                            return false;
                        }
                    }
                }
            } else {
                $contrUuid = $objectContr->contragentUuid;
            }

            $request = new Request();
            $request->scenario = ZhkhActiveRecord::SCENARIO_API;
            $request->oid = $realOid;
            $request->uuid = MainFunctions::GUID();
            $request->type = 0; // 0 - бесплатная заявка (может можно сечь по категориям интерсвязи для точного определения)
            $request->contragentUuid = $contrUuid;
            $request->authorUuid = Users::USER_SERVICE_UUID; // sUser
            $request->requestStatusUuid = RequestStatus::NEW_REQUEST; // Состояние (1 - новое, 2 - в работе, 3 - закрыто)
            $requestType = RequestType::findOne(['title' => 'Другой характер обращения', 'oid' => $realOid])->uuid;
            $request->requestTypeUuid = $requestType;
            $request->comment = $data['category']['text'] . ': ' . $incident['description'];
            $request->verdict = '';
            $request->result = '';
            $request->equipmentUuid = null;
            $request->objectUuid = $localObjectUuid;
            $request->extId = $data['id'];
            $request->integrationClass = self::class;
//            $request->taskUuid; // null
            $request->closeDate = null; // ? current_timestamp можно ли установить в null?
            if (!$request->save()) {
                // TODO: нужно как-то уведомить админа что что-то не сохранилось
//                return $request->errors;
                return false;
            }
        }

        return true;
    }

    /**
     * @param $data array массив с инфорацией об обращении
     * @return bool
     * @throws \Exception
     */
    private function updateAppeal($data)
    {
        // TODO: реализовать обновление данных по обращению
        // пока видно только три сущности которые меняются:
        // status(статус с ним всё ясно)
        // ownerUser(видимо ответственный, может быть и организацией и человеком) - решить
        // executorUser(исполнитель) - решить
        $statusUuid = null;
        switch ($data['status']) {
            case 1:
                $statusUuid = RequestStatus::NEW_REQUEST;
                break;
            case 2:
                $statusUuid = RequestStatus::IN_WORK;
                break;
            case 3:
                $statusUuid = RequestStatus::COMPLETE;
                break;
            default:
                return false;
                break;
        }

        $req = Request::find()->where(['extId' => $data['id']])->one();
        if ($req == null) {
            return false;
        }

        $req->requestStatusUuid = $statusUuid;
        if (!$req->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param $uuid string Uuid организации
     * @param $parameter string Названия параметра
     * @return Settings|null
     */
    private function getOrgSetting($uuid, $parameter)
    {
        return Settings::find()->where(['title' => $parameter . '-' . $uuid])->one();
    }
}
