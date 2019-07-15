<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\ObjectStatus;
use yii\db\ActiveRecord;

class ObjectStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = ObjectStatus::class;
}
