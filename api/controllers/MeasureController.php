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
        return parent::createBase();
    }
}
