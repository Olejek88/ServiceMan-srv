<?php

namespace api\controllers;

use api\components\BaseController;

class JournalController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'Journal'];
    }

}
