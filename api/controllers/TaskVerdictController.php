<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\TaskVerdict;
use yii\db\ActiveRecord;

class TaskVerdictController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = TaskVerdict::class;
}
