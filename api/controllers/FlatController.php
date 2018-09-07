<?php

namespace api\controllers;

use api\components\BaseController;

class FlatController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Flat'];
    }

}
