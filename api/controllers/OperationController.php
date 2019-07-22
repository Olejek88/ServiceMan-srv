<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Operation;
use yii\db\ActiveRecord;

class OperationController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Operation::class;
}
