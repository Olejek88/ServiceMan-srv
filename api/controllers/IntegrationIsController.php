<?php

namespace api\controllers;

use common\components\MainFunctions;
use common\components\ZhkhActiveRecord;
use common\models\Organization;
use common\models\Request;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Settings;
use common\models\Users;
use Yii;
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
     */
    public function actionNotify($orgUuid)
    {
        $request = Yii::$app->request;
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

        foreach ($data['incidents'] as $incident) {
            $request = new Request();
            $request->scenario = ZhkhActiveRecord::SCENARIO_API;
            $request->oid = $orgUuid;
            $request->uuid = MainFunctions::GUID();
            $request->type = 0; // 0 - бесплатная заявка (может можно сечь по категориям интерсвязи для точного определения)
            $request->contragentUuid = null; // null - посмотреть из каких данных можно создать контрагента в наших мульках
            $request->authorUuid = Users::USER_SERVICE_UUID; // sUser
            $request->requestStatusUuid = RequestStatus::NEW_REQUEST; // Состояние (1 - новое, 2 - в работе, 3 - закрыто)
            $request->requestTypeUuid = RequestType::findOne(['title' => 'Другой характер обращения'])->uuid;
            $request->comment = $data['category']['text'] . ': ' . $incident['description'] . '(' . $incident['address']['text'] . ')';
            $request->verdict = '';
            $request->result = '';
            $request->equipmentUuid = null;
//            $request->objectUuid; // null (вероятно можно найти по адресу указанному в запросе)
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
