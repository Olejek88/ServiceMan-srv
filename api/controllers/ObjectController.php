<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Objects;
use yii\db\ActiveRecord;

class ObjectController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Objects::class;

}
