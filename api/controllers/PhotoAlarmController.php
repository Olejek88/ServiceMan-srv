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
     * Во входных данных будет один PhotoAlarm. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     */
    public function actionCreate()
    {
        $request = \Yii::$app->request;

        // запись для загружаемого файла
        $photoAlarms = $request->getBodyParam('photoAlarms');
        $savedPhotoAlarms = parent::createSimpleObjects($photoAlarms);

        // сохраняем файл
        foreach ($photoAlarms as $photoAlarm) {
            if (!PhotoAlarm::saveUploadFile($photoAlarm['uuid'] . '.jpg')) {
                $savedPhotoAlarms = [
                    'success' => false,
                    'data' => []
                ];
            }
        }

        return  $savedPhotoAlarms;
    }
}
