<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoEquipment;
use yii\db\ActiveRecord;

class PhotoEquipmentController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoEquipment::class;
}
