<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\TaskType;
use yii\db\ActiveRecord;

class TaskTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = TaskType::class;
}
