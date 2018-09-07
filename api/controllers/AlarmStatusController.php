<?php

namespace api\controllers;

use api\components\BaseController;

class AlarmStatusController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'AlarmStatus'];
    }

}
