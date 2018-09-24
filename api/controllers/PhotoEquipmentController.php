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
