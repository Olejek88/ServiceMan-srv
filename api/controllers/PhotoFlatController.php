<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoFlat;
use yii\db\ActiveRecord;

class PhotoFlatController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoFlat::class;

    /**
     * Во входных данных будет один объект. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     */
    public function actionCreate()
    {
        return parent::createBasePhoto(PhotoFlat::getImageRoot());
    }
}
