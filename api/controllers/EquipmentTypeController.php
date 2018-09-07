<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\EquipmentType;
use yii\db\ActiveRecord;

class EquipmentTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = EquipmentType::class;
}
