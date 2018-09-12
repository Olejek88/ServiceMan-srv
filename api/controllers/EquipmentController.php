<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Equipment;
use yii\db\ActiveRecord;

class EquipmentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Equipment::class;

    /**
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBase();
    }
}
