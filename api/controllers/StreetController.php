<?php

namespace api\controllers;

use api\components\BaseController;

class StreetController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Street'];
    }

}
