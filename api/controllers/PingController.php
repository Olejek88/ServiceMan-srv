<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Alarm;

class PingController extends BaseController
{
    /** @var Alarm $modelClass */
    public $modelClass = null;

    public function actionIndex() {
        return 'PONG';
    }
}
