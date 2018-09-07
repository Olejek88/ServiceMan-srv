<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\HouseStatus;
use yii\db\ActiveRecord;

class HouseStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = HouseStatus::class;
}
