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

        // список записей
        $items = json_decode($rawData, true);
        if (!is_array($items)) {
            return [];
        }

        // сохраняем записи
        $saved = parent::createSimpleObjects($items);
        return $saved;
    }
}
