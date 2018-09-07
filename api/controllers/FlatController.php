<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Flat;
use yii\db\ActiveRecord;

class FlatController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Flat::class;
}
