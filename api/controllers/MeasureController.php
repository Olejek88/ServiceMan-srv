<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Measure;
use yii\db\ActiveRecord;

class MeasureController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Measure::class;

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
