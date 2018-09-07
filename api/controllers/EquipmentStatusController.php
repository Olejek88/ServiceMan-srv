<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\EquipmentStatus;
use yii\db\ActiveRecord;

class EquipmentStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = EquipmentStatus::class;
}
