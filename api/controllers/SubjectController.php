<?php

namespace api\controllers;

use api\components\BaseController;

class SubjectController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Subject'];
    }

}
