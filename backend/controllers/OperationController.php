<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\Operation;
use backend\models\OperationSearch;

class OperationController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {
        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

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
     * @return string|\yii\web\Response
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
