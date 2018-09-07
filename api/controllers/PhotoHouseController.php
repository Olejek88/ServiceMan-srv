<?php

namespace api\controllers;

use api\components\BaseController;

class PhotoHouseController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'PhotoHouse'];
    }

}
