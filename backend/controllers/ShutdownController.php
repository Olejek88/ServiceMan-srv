<?php

namespace backend\controllers;

use backend\models\ShutdownSearch;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Shutdown;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * ShutdownController implements the CRUD actions for Shutdown model.
 */
class ShutdownController extends ZhkhController
{
    protected $modelClass = Shutdown::class;

    /**
     * Lists all Shutdown models.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Shutdown::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'contragent') {
                $model['contragentUuid'] = $_POST['Shutdown'][$_POST['editableIndex']]['contragent'];
            }
            if ($_POST['editableAttribute'] == 'startDate') {
                $model['startDate'] = $_POST['Shutdown'][$_POST['editableIndex']]['startDate'];
            }
            if ($_POST['editableAttribute'] == 'endDate') {
                $model['endDate'] = $_POST['Shutdown'][$_POST['editableIndex']]['endDate'];
            }
            $model->save();
            return json_encode('');
        }
        $searchModel = new ShutdownSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shutdown model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Shutdown model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Shutdown();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new ShutdownSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 15;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Shutdown model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Shutdown model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Shutdown model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shutdown the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shutdown::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionForm()
    {
        $model = new Shutdown();
        $contragents = Contragent::find()
            ->where(['IN', 'contragentTypeUuid', [
                ContragentType::CONTRACTOR,
                ContragentType::ORGANIZATION
            ]])
            ->andWhere(['deleted' => 0])
            ->all();

        return $this->renderAjax('_add_shutdown', ['model' => $model, 'contragents' => $contragents]);
    }

    /**
     * Creates a new Shutdown model.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionNew()
    {
        if (isset($_POST['shutdownUuid']))
            $model = Shutdown::find()->where(['uuid' => $_POST['shutdownUuid']])->one();
        else
            $model = new Shutdown();
        if ($model->load(Yii::$app->request->post())) {
            $model->save(false);
        }
    }
}
