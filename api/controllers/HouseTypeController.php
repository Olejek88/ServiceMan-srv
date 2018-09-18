<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\HouseType;
use yii\db\ActiveRecord;

class HouseTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = HouseType::class;
}
