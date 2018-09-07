<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\City;
use yii\db\ActiveRecord;

class CityController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = City::class;
}
