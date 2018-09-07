<?php

namespace api\controllers;

use api\components\BaseController;

class HouseStatusController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'HouseStatus'];
    }

}
