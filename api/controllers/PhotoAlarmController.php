<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoAlarm;
use yii\db\ActiveRecord;

class PhotoAlarmController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoAlarm::class;

    /**
     * Во входных данных будет один объект. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBasePhoto(PhotoAlarm::getImageRoot());
    }
}
