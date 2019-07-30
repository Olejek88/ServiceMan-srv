<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\RequestType;
use yii\db\ActiveRecord;

class RequestTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = RequestType::class;
}
