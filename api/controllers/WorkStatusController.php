<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\WorkStatus;
use yii\db\ActiveRecord;

class WorkStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = WorkStatus::class;
}
