<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\House;
use yii\db\ActiveRecord;

class HouseController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = House::class;
}
