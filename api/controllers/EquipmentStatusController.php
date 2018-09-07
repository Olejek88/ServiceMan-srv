<?php

namespace api\controllers;

use api\components\BaseController;

class EquipmentStatusController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'EquipmentStatus'];
    }

}
