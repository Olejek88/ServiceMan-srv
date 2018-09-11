<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Alarm;
use yii\db\ActiveRecord;

class AlarmController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Alarm::class;

    public function actionCreate()
    {
        parent::createSimpleObject();
    }
}
