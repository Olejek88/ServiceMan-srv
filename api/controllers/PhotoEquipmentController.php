<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoEquipment;
use yii\db\ActiveRecord;

class PhotoEquipmentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoEquipment::class;

    /**
     * Во входных данных будет один объект. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBasePhoto(PhotoEquipment::getImageRoot());
    }
}
