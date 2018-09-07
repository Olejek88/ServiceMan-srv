<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Street;
use yii\db\ActiveRecord;

class StreetController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Street::class;
}
