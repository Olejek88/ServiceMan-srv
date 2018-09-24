<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoAlarm;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;

class PhotoAlarmController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoAlarm::class;

    /**
     * Во входных данных должен быть один объект.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionCreate()
    {
        return parent::createBasePhoto();
    }
}
