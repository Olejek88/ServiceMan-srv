<?php

namespace api\controllers;

use api\components\BaseController;

class PhotoAlarmController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'PhotoAlarm'];
    }

}
