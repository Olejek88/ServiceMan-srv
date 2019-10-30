<?php

namespace api\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class IntegrationIsController extends Controller
{
    // TODO: переопределить все штатные действия

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

    public function actionNotify($orgUuid)
    {
        // TODO: реализовать проверку ip клиента
        $request = Yii::$app->request;
        $operation = $request->getBodyParam('operation');
        $object = $request->getBodyParam('object');
        $data = $request->getBodyParam('data');

        // TODO: реализовать создание заявки по полученным данным в нашей системе
        return [
            'oid' => $orgUuid,
            'operation' => $operation,
            'object' => $object,
            'data' => $data,
        ];
    }

}
