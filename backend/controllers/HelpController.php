<?php

namespace backend\controllers;

use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
 */
class HelpController extends Controller
{
    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Equipment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = 'Hello';

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
