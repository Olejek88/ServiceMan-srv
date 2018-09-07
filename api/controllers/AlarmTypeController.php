<?php

namespace api\controllers;

use api\components\BaseController;

class AlarmTypeController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'AlarmType'];
    }

}
