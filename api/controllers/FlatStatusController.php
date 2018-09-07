<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\FlatStatus;
use yii\db\ActiveRecord;

class FlatStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = FlatStatus::class;
}
