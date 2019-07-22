<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\User;
use common\models\UserHouse;
use yii\db\ActiveRecord;

class UserHouseController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = UserHouse::class;

}
