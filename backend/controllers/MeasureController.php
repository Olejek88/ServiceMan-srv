<?php

namespace backend\controllers;

use backend\models\MeasureSearch;
use common\models\Measure;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use Throwable;

/**
 * MeasureController implements the CRUD actions for Measure model.
 */
class MeasureController extends ZhkhController
{
    /**
     * Lists all Measure models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MeasureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 200;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=','date',$_GET['start_time']]);
            $dataProvider->query->andWhere(['<','date',$_GET['end_time']]);
        }
        if (isset($_GET['type']) && $_GET['type']!='') {
            $dataProvider->query->andWhere(['=','measureTypeUuid',$_GET['type']]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Measure model.
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
     * Creates a new Measure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Measure();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return self::actionIndex();
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays a trend of value
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTrend()
    {
        $measure = array(0);
        $name = '';
        if ($_GET["equipment"]) {
            $measure = Measure::find()
                ->where(['equipmentUuid' => $_GET["equipment"]])
                ->orderBy('date')
                ->all();
            if ($measure[0] != null)
                $name = $measure[0]['equipment']['equipmentType']->title;
        }
        return $this->render('trend', [
            'values' => $measure,
            'name' => $name
        ]);
    }

    /**
     * Updates an existing Measure model.
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
     * Deletes an existing Measure model.
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
     * Finds the Measure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Measure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Measure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return bool|string
     */
    public
    function actionAdd()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = new Measure();
            $source = "../equipment";
            if ($_GET['source'])
                $source = $_GET['source'];
            return $this->renderAjax('_add_form', [
                'model' => $model,
                'equipmentUuid' => $_GET["equipmentUuid"],
                'source' => $source
            ]);
        }
        return false;
    }

    /**
     * Creates a new Measure model.
     * @return mixed
     */
    public
    function actionSave()
    {
        if (isset($_POST["source"]))
            $source = $_POST["source"];
        else $source = "../equipment";

        $model = new Measure();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false))
                return $this->redirect($source);
            }
        return $this->redirect($source);
    }
}
