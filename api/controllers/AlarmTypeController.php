<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\AlarmType;
use yii\db\ActiveRecord;

class AlarmTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = AlarmType::class;
}
