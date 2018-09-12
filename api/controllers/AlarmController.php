<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Alarm;

class AlarmController extends BaseController
{
    /** @var Alarm $modelClass */
    public $modelClass = Alarm::class;

    /**
     * @return array
     */
    public function actionCreate()
    {
        $request = \Yii::$app->request;

        $rawData = $request->getRawBody();
        if ($rawData == null) {
            return [];
        }

        // список записей сообщений
        $alarms = json_decode($rawData, true);
        if (!is_array($alarms)) {
            return [];
        }

        // сохраняем записи
        $savedAlarms = parent::createSimpleObjects($alarms);
        return $savedAlarms;
    }
}
