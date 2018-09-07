<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Subject;
use yii\db\ActiveRecord;

class SubjectController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Subject::class;
}
