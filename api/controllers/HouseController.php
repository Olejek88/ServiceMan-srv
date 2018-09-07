<?php

namespace api\controllers;

use api\components\BaseController;

class HouseController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'House'];
    }

}
