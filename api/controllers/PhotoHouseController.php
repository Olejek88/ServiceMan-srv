<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoHouse;
use yii\db\ActiveRecord;

class PhotoHouseController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoHouse::class;

    /**
     * Во входных данных будет один объект. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBasePhoto(PhotoHouse::getImageRoot());
    }
}
