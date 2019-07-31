<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\RequestStatus;
use yii\db\ActiveRecord;

class RequestStatusController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = RequestStatus::class;
}
