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
        $request = \Yii::$app->request;

        // запись для загружаемого файла
        $photos = $request->getBodyParam('photos');
        $savedPhotos = parent::createSimpleObjects($photos);

        // сохраняем файл
        foreach ($photos as $photo) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if (!parent::saveUploadFile($photo['uuid'] . '.' . $ext, PhotoAlarm::getImageRoot())) {
                $savedPhotos = [
                    'success' => false,
                    'data' => []
                ];
            }
        }

        return $savedPhotos;
    }
}
