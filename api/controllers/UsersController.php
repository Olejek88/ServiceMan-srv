<?php

namespace api\controllers;

use api\components\BaseController;

class UsersController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Users'];
    }

}
