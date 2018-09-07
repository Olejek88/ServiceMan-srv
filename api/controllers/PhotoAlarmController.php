<?php

namespace api\controllers;

use api\components\BaseController;
use yii\db\ActiveRecord;

class PhotoAlarmController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoAlarmController::class;
}
