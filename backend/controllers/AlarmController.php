<?php

namespace backend\controllers;

use backend\models\AlarmSearch;
use common\components\MainFunctions;
use common\models\Alarm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use Throwable;
use yii\db\StaleObjectException;

/**
 * AlarmController implements the CRUD actions for Alarm model.
 */
class AlarmController extends ZhkhController
{
    /**
     * Lists all Alarm models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlarmSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Alarm model.
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
     * Creates a new Alarm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Alarm();
        $searchModel = new AlarmSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MainFunctions::register('alarm','Зарегистрирована авария',
                '<a class="btn btn-default btn-xs">'.$model['alarmType']['title'].'</a>'.$model->comment.'<br/>'.
                '<a class="btn btn-default btn-xs">' . $model['object']['title'] . '</a>', $model['uuid']);
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model, 'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing Alarm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        parent::actionUpdate($id);

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
     * Deletes an existing Alarm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Alarm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Alarm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alarm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
