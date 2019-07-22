<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\OperationTemplate;
use yii\db\ActiveRecord;

class OperationTemplateController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = OperationTemplate::class;
}
