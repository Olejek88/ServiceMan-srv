<?php

namespace backend\controllers;

use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class InformationController extends Controller
{
    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
