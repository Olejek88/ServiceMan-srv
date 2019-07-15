<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\MeasureType;
use yii\db\ActiveRecord;

class MeasureTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = MeasureType::class;
}
