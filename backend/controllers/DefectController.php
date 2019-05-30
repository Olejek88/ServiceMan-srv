<?php

namespace backend\controllers;

use backend\models\DefectSearch;
use common\components\MainFunctions;
use common\models\Defect;
use common\models\DefectType;
use common\models\Equipment;
use common\models\Orders;
use common\models\Stage;
use common\models\Task;
use common\models\Users;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DefectController implements the CRUD actions for Defect model.
 */
class DefectController extends Controller
{
    protected $modelClass = Defect::class;

    /**
     * Lists all Defect models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DefectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Defect models.
     * @param $equipmentUuid
     * @return mixed
     */
    public function actionList($equipmentUuid)
    {
        $defects = Defect::find()
            ->select('*')
            ->where(['equipmentUuid' => $equipmentUuid])
            ->all();
        return $this->renderAjax('_defects_list', [
            'defects' => $defects,
        ]);
    }

    /**
     * Displays a single Defect model.
     * @param string $uuid
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($uuid)
    {
        return $this->render('view', [
            'model' => $this->findModel($uuid),
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
        $defect = Defect::find()
            ->select('_id')
            ->orderBy('_id DESC')
            ->one();
        if ($defect)
            $defect_id = $defect["_id"] + 1;
        else
            $defect_id = 1;
        $model["_id"] = $defect_id;

        $searchModel = new DefectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 10;
        $dataProvider->setSort(['defaultOrder' => ['_id'=>SORT_DESC]]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id, 'uuid' => $model->uuid]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing Defect model.
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
     * Deletes an existing Defect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Defect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $uuid
     * @return Defect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($uuid)
    {
        if (($model = Defect::findOne(['uuid' => $uuid])) !== null) {
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
        $sum = 0;
        foreach ($defectsByModel as $defect) {
            $sum += $defect['cnt'];
        }
        $cnt = 0;
        foreach ($defectsByModel as $defect) {
            $defectsByModel[$cnt]['cnt'] = $defect['cnt'] * 100 / $sum;
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
        $sum = 0;
        foreach ($defectsByType as $defect) {
            $sum += $defect['cnt'];
        }
        $cnt = 0;
        foreach ($defectsByType as $defect) {
            $defectsByType[$cnt]['cnt'] = $defect['cnt'] * 100 / $sum;
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

    /** Check add defect
     * @var $model Defect
     * @return mixed
     */
    public function actionSave()
    {
        $model = new Defect();
        $request = Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            $defect = Defect::find()
                ->select('_id')
                ->orderBy('_id DESC')
                ->one();
            if ($defect)
                $defect_id = $defect["_id"] + 1;
            else
                $defect_id = 1;
            $model->_id = $defect_id;
            if (isset($_POST["Defect"]["equipmentUuid"]))
                $model->equipmentUuid = $_POST["Defect"]["equipmentUuid"];
            if (isset($_POST["Defect"]["userUuid"]))
                $model->userUuid = $_POST["Defect"]["userUuid"];
            $model->title = $_POST["Defect"]["title"];
            $model->defectStatus = $_POST["Defect"]["defectStatus"];
            $model->uuid = MainFunctions::GUID();
            $model->date = date('Y-m-d\TH:i:s');
            if ($model->save(false)) {
                return $this->redirect(['/equipment/tree']);
            }
        }
        return false;
    }

    public function actionAdd()
    {
        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["folder"]))
                $folder = $_POST["folder"];
            else $folder = 0;
            if ($folder == "false") {
                $defect = new Defect();
                return $this->renderAjax('../defect/_add_form', [
                    'model' => $defect,
                    'equipmentUuid' => $uuid
                ]);
            }
        }
        return "Выберите в дереве оборудование";
    }

}
