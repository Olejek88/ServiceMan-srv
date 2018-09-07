<?php

namespace api\controllers;

use api\components\BaseController;

class CityController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'city'];
    }
}
