<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Gpstrack;
use yii\db\ActiveRecord;

class GpsTrackController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Gpstrack::class;

    public function actionIndex()
    {
        // данные журнала ни когда не отправляются на клиента
        return [];
    }

    public function actionCreate()
    {
        return parent::createSimpleObject();
    }
}
