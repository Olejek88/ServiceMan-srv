<?php

namespace common\modules\selectdb\controllers;

use yii\web\Controller;

/**
 * Default controller for the `toir` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('dashboard');
    }
}
