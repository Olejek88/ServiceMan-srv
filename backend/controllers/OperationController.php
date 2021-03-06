<?php
namespace backend\controllers;

use backend\models\OperationSearch;
use common\models\Operation;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OperationController extends ZhkhController
{
    protected $modelClass = Operation::class;

    /**
     * Lists all Operation models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OperationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Operation model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render(
            'view',
            [
                'model' => $model
            ]
        );
    }

    /**
     * Action info.
     *
     * @param integer $id Id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInfo($id)
    {
        return $this->render(
            'info',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Action search.
     *
     * @return string
     */
    public function actionSearch()
    {
        return $this->render('search');
    }

    /**
     * Action generate.
     *
     * @return string|Response
     */
    public function actionGenerate()
    {
        $model = new Operation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect('/orders');
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('generate', ['model' => $model,]);
        }
    }


    /**
     * Creates a new Operation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Operation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', ['model' => $model,]);
        }
    }

    /**
     * Updates an existing Operation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', ['model' => $model,]);
        }
    }

    /**
     * Deletes an existing Operation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Operation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Operation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Operation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
