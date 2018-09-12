<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoEquipment;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;

class PhotoEquipmentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoEquipment::class;

    /**
     * Во входных данных будет один объект. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionCreate()
    {
        return parent::createBasePhoto();
    }
}
