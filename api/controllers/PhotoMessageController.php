<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoMessage;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;

class PhotoMessageController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoMessage::class;

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
