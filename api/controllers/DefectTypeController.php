<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\DefectType;
use yii\db\ActiveRecord;

class DefectTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = DefectType::class;
}
