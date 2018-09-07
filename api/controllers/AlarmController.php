<?php

namespace api\controllers;

use api\components\BaseController;

class AlarmController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Alarm'];
    }

}
