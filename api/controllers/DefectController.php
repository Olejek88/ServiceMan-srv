<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Defect;
use yii\db\ActiveRecord;

class DefectController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Defect::class;
}
