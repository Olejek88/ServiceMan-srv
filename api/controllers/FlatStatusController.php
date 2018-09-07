<?php

namespace api\controllers;

use api\components\BaseController;

class FlatStatusController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'FlatStatus'];
    }

}
