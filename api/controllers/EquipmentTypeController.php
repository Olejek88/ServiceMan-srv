<?php

namespace api\controllers;

use api\components\BaseController;

class EquipmentTypeController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'EquipmentType'];
    }

}
