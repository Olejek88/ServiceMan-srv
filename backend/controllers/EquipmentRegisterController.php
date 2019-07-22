<?php

namespace backend\controllers;

use backend\models\EquipmentRegisterSearch;
use common\components\MainFunctions;
use common\models\EquipmentRegister;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\db\Exception;
use Throwable;

/**
 * EquipmentRegisterController implements the CRUD actions for EquipmentRegister model.
 */
class EquipmentRegisterController extends ZhkhController
{
    /**
     * Lists all EquipmentRegister models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipmentRegisterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipmentRegister model.
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
     * Creates a new EquipmentRegister model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new EquipmentRegister();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipmentRegister model.
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
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipmentRegister model.
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
     * Finds the EquipmentRegister model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipmentRegister the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentRegister::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all EquipmentRegister models for Equipment .
     * @param $equipmentUuid
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionList($equipmentUuid)
    {
        $registers = EquipmentRegister::find()
            ->select('*')
            ->where(['equipmentUuid' => $equipmentUuid])
            ->all();
        MainFunctions::log("main.log",date("Y-m-d H:i:s").' '.$equipmentUuid.' '.$_GET["equipmentUuid"]);
        return $this->renderAjax('_register_list', [
            'registers' => $registers,
            'equipmentUuid' => $equipmentUuid
        ]);
    }

    /**
     * Creates a new EquipmentRegister model.
     * @return mixed
     * @var $model EquipmentRegister
     */
    public function actionNew()
    {
        $model = new EquipmentRegister();
        $request = Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            if (isset($_POST["EquipmentRegister"]["equipmentUuid"]))
                $model->equipmentUuid = $_POST["EquipmentRegister"]["equipmentUuid"];
            if (isset($_POST["EquipmentRegister"]["userUuid"]))
                $model->userUuid = $_POST["EquipmentRegister"]["userUuid"];
            $model->description = $_POST["EquipmentRegister"]["description"];
            $model->registerTypeUuid = $_POST["EquipmentRegister"]["registerTypeUuid"];
            $model->uuid = MainFunctions::GUID();
            $model->date = date('Y-m-d\TH:i:s');
            if ($model->validate() && $model->equipmentUuid) {
                $model->save();
                return json_encode($model->errors);
            }
            return false;
        }
        return false;
    }

    public function actionForm()
    {
        $model = new EquipmentRegister();
        if (isset($_GET["equipmentUuid"]))
            $model->equipmentUuid = $_GET["equipmentUuid"];
        if (isset($_GET["user"]))
            $model->userUuid = $_GET["user"];
        return $this->renderAjax('_add_register', [
            'model' => $model,
            'equipmentUuid' => $_GET["equipmentUuid"]
        ]);
    }

    /**
     * @param $equipmentUuid
     * @param $registerTypeUuid
     * @param $description
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function addEquipmentRegister($equipmentUuid, $registerTypeUuid, $description)
    {
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::find()
            ->where(['user_id' => $accountUser['id']])
            ->asArray()
            ->one();

        $model = new EquipmentRegister();
        $model->equipmentUuid = $equipmentUuid;
        $model->userUuid = $currentUser['uuid'];
        $model->description = $description;
        $model->registerTypeUuid = $registerTypeUuid;
        $model->uuid = MainFunctions::GUID();
        $model->date = date('Y-m-d\TH:i:s');
        $model->oid = Users::getCurrentOid();
        $model->save();
    }
}
