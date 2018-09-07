<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\PhotoHouse;
use yii\db\ActiveRecord;

class PhotoHouseController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = PhotoHouse::class;
}
