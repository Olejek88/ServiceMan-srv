<?php

namespace api\controllers;

use api\components\BaseController;

class EquipmentController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Equipment'];
    }

}
