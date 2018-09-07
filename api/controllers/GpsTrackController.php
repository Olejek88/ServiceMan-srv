<?php

namespace api\controllers;

use api\components\BaseController;

class GpsTrackController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'GpsTrack'];
    }

}
