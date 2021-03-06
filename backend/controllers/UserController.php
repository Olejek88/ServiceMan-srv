<?php

namespace backend\controllers;

use common\models\User;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UserController extends ZhkhController
{
    protected $modelClass = User::class;

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['users/dashboard']);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->redirect(['users/dashboard']);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->redirect(['users/dashboard']);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->redirect(['users/dashboard']);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->redirect(['users/dashboard']);
    }

}
