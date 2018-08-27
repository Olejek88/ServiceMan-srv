<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\Users;
use common\models\Defect;
use common\models\DefectType;
use common\models\Equipment;

use backend\models\DefectSearch;

/**
 * DefectController implements the CRUD actions for Defect model.
 */
class DefectController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Defect models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DefectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Defect model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $defect = Defect::find()
            ->select('userUuid,equipmentUuid,defectTypeUuid')
            ->where(['_id' => $id])
            ->asArray()
            ->one();
        $user = Users::find()
            ->select('name')
            ->where(['uuid' => $defect['userUuid']])
            ->asArray()
            ->one();
        $defectType = DefectType::find()
            ->select('title')
            ->where(['uuid' => $defect['defectTypeUuid']])
            ->asArray()
            ->one();
        $equipment = Equipment::find()
            ->select('title')
            ->where(['uuid' => $defect['equipmentUuid']])
            ->asArray()
            ->one();

        return $this->render('view', [
            'equipment' => $equipment,
            'user' => $user,
            'defectType' => $defectType,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Defect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Defect();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Defect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
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
     * Deletes an existing Defect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Defect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Defect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Defect::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Displays a schedule for all users
     * @return mixed
     */
    public function actionPie()
    {
        $defectsByModel = Defect::find()
            ->select('COUNT(*) AS cnt, equipmentUuid, equipment_model.title AS title')
            ->leftJoin('equipment', 'equipment.uuid=defect.equipmentUuid')
            ->leftJoin('equipment_model', 'equipment_model.uuid=equipment.equipmentModelUuid')
            ->asArray()
            ->groupBy('equipment_model.title')
            ->all();
        $sum=0;
        foreach ($defectsByModel as $defect) {
            $sum+=$defect['cnt'];
        }
        $cnt=0;
        foreach ($defectsByModel as $defect) {
            $defectsByModel[$cnt]['cnt']=$defect['cnt']*100/$sum;
            $cnt++;
        }

        $defectsByType = Defect::find()
            ->select('COUNT(*) AS cnt, equipment_type.title AS title')
            ->leftJoin('equipment', 'equipment.uuid=defect.equipmentUuid')
            ->leftJoin('equipment_model', 'equipment_model.uuid=equipment.equipmentModelUuid')
            ->leftJoin('equipment_type', 'equipment_type.uuid=equipment_model.equipmentTypeUuid')
            ->asArray()
            ->groupBy('equipment_type.title')
            ->all();
        $sum=0;
        foreach ($defectsByType as $defect) {
            $sum+=$defect['cnt'];
        }
        $cnt=0;
        foreach ($defectsByType as $defect) {
            $defectsByType[$cnt]['cnt']=$defect['cnt']*100/$sum;
            $cnt++;
        }

        return $this->render('pie', [
            'defectsByType' => $defectsByType,
            'defectsByModel' => $defectsByModel,
        ]);

    }

    /**
     * Display defects as bar
     * @return mixed
     */
    public function actionBargraph()
    {
        $allDefects = Defect::find()
            ->select('COUNT(*) AS cnt, defect_type.title AS title')
            ->leftJoin('defect_type', 'defect_type.uuid=defect.defectTypeUuid')
            ->asArray()
            ->groupBy('defectTypeUuid')
            ->all();

        return $this->render('bargraph', [
            'defects' => $allDefects,
        ]);
    }

}
