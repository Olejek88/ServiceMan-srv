<?php

namespace api\controllers;

use api\components\BaseController;

class ResidentController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Resident'];
    }

}
