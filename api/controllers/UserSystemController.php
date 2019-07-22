<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\UserSystem;
use yii\db\ActiveRecord;

class UserSystemController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = UserSystem::class;
}
