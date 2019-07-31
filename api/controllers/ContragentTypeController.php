<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\ContragentType;
use yii\db\ActiveRecord;

class ContragentTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = ContragentType::class;
}
