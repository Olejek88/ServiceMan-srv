<?php

namespace backend\controllers;

use backend\models\DefectSearch;
use common\components\MainFunctions;
use common\models\Defect;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * DefectController implements the CRUD actions for Defect model.
 */
class DefectController extends ZhkhController
{
    protected $modelClass = Defect::class;

    /**
     * Lists all Defect models.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex()
    {
        $searchModel = new DefectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%'.$_GET['address'].'%',false],
                    ['like', 'object.title', '%'.$_GET['address'].'%',false],
                    ['like', 'street.title', '%'.$_GET['address'].'%',false]]
            );
        }
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'date', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'date', $_GET['end_time']]);
        }
        if (isset($_POST['editableAttribute'])) {
            $model = Defect::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'defectStatus') {
                $model['defectStatus'] = $_POST['Defect'][$_POST['editableIndex']]['defectStatus'];
            }
            $model->save();
            return "1";
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Defect models.
     * @param $equipmentUuid
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
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
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Defect();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id, 'uuid' => $model->uuid]);
        } else {
            $searchModel = new DefectSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 10;
            $dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);
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
     * @throws Throwable
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
     * @throws Exception
     * @throws InvalidConfigException
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
     * @throws Exception
     * @throws InvalidConfigException
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
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionSave()
    {
        $model = new Defect();
        $request = Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            if (isset($_POST["Defect"]["equipmentUuid"]))
                $model->equipmentUuid = $_POST["Defect"]["equipmentUuid"];
            if (isset($_POST["Defect"]["userUuid"]))
                $model->userUuid = $_POST["Defect"]["userUuid"];
            $model->title = $_POST["Defect"]["title"];
            $model->defectStatus = $_POST["Defect"]["defectStatus"];
            $model->uuid = MainFunctions::GUID();
            $model->date = date('Y-m-d\TH:i:s');
            if ($model->save(false)) {
                if (isset($_POST['source']))
                    return $this->redirect($_POST['source']);
                return $this->redirect('../equipment/tree');
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
            if (isset($_POST["source"]))
                $source = $_POST["source"];
            else $source = '../equipment/tree';

            if ($folder == "false") {
                $defect = new Defect();
                return $this->renderAjax('../defect/_add_form', [
                    'model' => $defect,
                    'source' => $source,
                    'equipmentUuid' => $uuid
                ]);
            }
        }
        return "Выберите в дереве оборудование";
    }

    public function actionAddTable()
    {
        if (isset($_GET["uuid"]))
            $uuid = $_GET["uuid"];
        else $uuid = 0;
        $defect = new Defect();
        return $this->renderAjax('../defect/_add_form', [
            'model' => $defect,
            'equipmentUuid' => $uuid,
            'source' => '../equipment'
        ]);
    }

}
