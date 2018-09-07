<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Users;
use yii\db\ActiveRecord;

class UsersController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Users::class;
}
