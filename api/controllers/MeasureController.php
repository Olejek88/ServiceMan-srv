<?php

namespace api\controllers;

use api\components\BaseController;

class MeasureController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Measure'];
    }

}
