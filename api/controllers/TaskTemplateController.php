<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\TaskTemplate;
use yii\db\ActiveRecord;

class TaskTemplateController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = TaskTemplate::class;

}
