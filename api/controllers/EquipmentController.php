<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Equipment;
use common\models\User;
use yii\db\ActiveRecord;

class EquipmentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Equipment::class;

}
