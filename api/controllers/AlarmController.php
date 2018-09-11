<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Alarm;
use common\models\PhotoAlarm;
use yii\db\ActiveRecord;
//use yii\web\NotAcceptableHttpException;

class AlarmController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Alarm::class;

    /**
     * @return array
//     * @throws NotAcceptableHttpException
     */
    public function actionCreate()
    {
        $request = \Yii::$app->request;

        // список записей сообщений
        $alarms = $request->getBodyParam('alarms');
        $savedAlarms = parent::createSimpleObjects($alarms);

        // принудительно меняем класс с которым работает контроллер чтобы создавались правильные модели
        $this->modelClass = PhotoAlarm::class;
        // список записей для загружаемых файлов
        $photoAlarms = $request->getBodyParam('photoAlarms');
//        if ($_FILES == null && $photoAlarms == null || (count($_FILES['photoAlarms']['name']) != count($photoAlarms))) {
//            throw new NotAcceptableHttpException();
//        }

        $savedFiles = parent::createSimpleObjects($photoAlarms);

        // TODO: реализовать загрузку и сохранение файлов фотографий

        return [
            'success' => $savedAlarms['success'] && $savedFiles['success'],
            'data' => [
                'alarms' => $savedAlarms,
                'file' => $savedFiles,
            ],
        ];
    }
}
