<?php

namespace api\controllers;

use api\components\BaseController;

class PhotoFlatController extends BaseController
{
    public function actionIndex()
    {
        return ['message' => 'PhotoFlat'];
    }

}
