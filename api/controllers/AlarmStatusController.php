<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\AlarmStatus;
use yii\db\ActiveRecord;

class AlarmStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = AlarmStatus::class;
}
