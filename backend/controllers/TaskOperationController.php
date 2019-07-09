<?php

namespace backend\controllers;

use backend\models\TaskOperationSearch;
use common\models\TaskOperation;
use Yii;
/**
 * TaskOperationController implements the CRUD actions for TaskOperation model.
 */
class TaskOperationController extends ZhkhController
{
    /**
     * Lists all TaskOperation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskOperationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaskOperation model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $model = TaskOperation::find()->where(['_id' => $id])->one();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new TaskOperation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new TaskOperation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TaskOperation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        parent::actionUpdate($id);

        $model = TaskOPeration::find()->where(['_id' => $id])->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model['id']]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TaskOperation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

        $model = TaskOperation::find()->where(['_id' => $id])->one();

        $model->delete();

        return $this->redirect(['index']);
    }

}
