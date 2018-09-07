<?php

namespace api\controllers;

use api\components\BaseController;

class PhotoEquipmentController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'PhotoEquipment'];
    }

}
