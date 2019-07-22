<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\ObjectType;
use yii\db\ActiveRecord;

class ObjectTypeController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = ObjectType::class;
}
