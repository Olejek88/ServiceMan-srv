<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\EquipmentStatus;
use common\models\EquipmentSystem;
use yii\db\ActiveRecord;

class EquipmentSystemController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = EquipmentSystem::class;
}
