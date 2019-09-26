<?php

namespace backend\controllers;

use backend\models\TaskTemplateEquipmentTypeSearch;
use common\components\Errors;
use common\models\TaskTemplateEquipmentType;
use Exception;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * TaskTemplateEquipmentTypeController implements the CRUD actions for
 * TaskTemplateEquipmentType model.
 */
class TaskTemplateEquipmentTypeController extends ZhkhController
{
    protected $modelClass = TaskTemplateEquipmentType::class;

    // отключаем проверку для внешних запросов

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        if ($action->id === 'index' || $action->id === 'create'
            || $action->id === 'update' || $action->id === 'delete') {
            $this->enableCsrfValidation = true;
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all TaskTemplateEquipmentType models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskTemplateEquipmentTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single TaskTemplateEquipmentType model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new TaskTemplateEquipmentType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaskTemplateEquipmentType();
        $searchModel = new TaskTemplateEquipmentTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 10;
        $dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model, 'dataProvider' => $dataProvider
                ]
            );
        }
    }

    /**
     * Updates an existing TaskTemplateEquipmentType model.
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
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing TaskTemplateEquipmentType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TaskTemplateEquipmentType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return TaskTemplateEquipmentType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskTemplateEquipmentType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string
     */
    public function actionForm()
    {
        if (isset($_GET["equipmentTypeUuid"])) {
            $model = new TaskTemplateEquipmentType();
            return $this->renderAjax('_add_form', ['model' => $model, 'equipmentTypeUuid' => $_GET["equipmentTypeUuid"]]);
        }
        return "";
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionNew()
    {
        $model = new TaskTemplateEquipmentType();
        if ($model->load(Yii::$app->request->post())) {
            $model->save(false);
        }
        return true;
    }

    /**
     * функция отрабатывает сигнал от дерева редактирования TaskTemplate
     * POST string $uuid - задачи
     * @return mixed
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteTask()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = TaskTemplateEquipmentType::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            } else
                return Errors::ERROR_SAVE;
        } else return Errors::WRONG_INPUT_PARAMETERS;
    }
}
