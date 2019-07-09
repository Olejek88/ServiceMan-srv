<?php

namespace backend\controllers;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
 */
class HelpController extends ZhkhController
{
    /**
     * Lists all Equipment models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
