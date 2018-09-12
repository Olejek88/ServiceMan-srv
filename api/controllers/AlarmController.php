<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Alarm;

class AlarmController extends BaseController
{
    /** @var Alarm $modelClass */
    public $modelClass = Alarm::class;

    /**
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBase();
    }
}
