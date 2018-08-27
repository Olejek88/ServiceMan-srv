<?php

namespace backend\controllers;

use yii\web\UnauthorizedHttpException;
use yii\web\Controller;

class InformationController extends Controller
{
    public function init() {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }
    
    public function actionIndex()
    {
        return $this->render('index');
    }

}
