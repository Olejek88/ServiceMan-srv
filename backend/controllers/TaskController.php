<?php
namespace backend\controllers;

use common\models\Object;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\Task;
use common\models\Equipment;
use common\models\WorkStatus;
use common\models\Operation;
use common\models\OperationTemplate;
use backend\models\TaskSearch;

class TaskController extends Controller
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
     * Lists all Task models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
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
     * Displays a single task model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = task::findOne($id);
        $task = Task::findOne(['uuid' => $model['taskUuid']]);
        return $this->render(
            'view',
            [
                'task' => $task,
                'model' => $model,
            ]
        );
    }

    /**
     * Info
     *
     * @param integer $id Id
     *
     * @return string
     */
    public function actionInfo($id)
    {
        $task = Task::find()
            ->select('uuid, taskUuid, equipmentUuid, workStatusUuid')
            ->where(['_id' => $id])
            ->asArray()
            ->one();

        $equipment = Equipment::find()
            ->select('title')
            ->where(['uuid' => $task['equipmentUuid']])
            ->asArray()
            ->one();

        $flat = Object::find()
            ->select('title')
            ->where(['uuid' => $task['flatUuid']])
            ->asArray()
            ->one();

        $statusTitle = WorkStatus::find()
            ->select('title')
            ->where(['uuid' => $task['taskStatusUuid']])
            ->asArray()
            ->one();
        /**
         * Выборка задач, этапов и операций для определенного наряда
         */
        $operationsFind = Operation::find()
            ->where(['stageUuid' => $task['uuid']])
            ->asArray()
            ->all();

        $operationTemp = OperationTemplate::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $workStatus = WorkStatus::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        foreach ($operationsFind as $key => $operation) {
            foreach ($operationTemp as $template) {
                if ($operation['operationTemplateUuid'] === $template['uuid']) {
                    $operationsFind[$key]['operationTemplateUuid'] = $template['title'];
                }
            }

            foreach ($workStatus as $status) {
                if ($operation['workStatusUuid'] === $status['uuid']) {
                    $operationsFind[$key]['workStatusUuid'] = $status['title'];
                }
            }
        }

        return $this->render(
            'info',
            [
                'task' => $task,
                'flat' => $flat,
                'equipment' => $equipment,
                'status' => $statusTitle,
                'operations' => $operationsFind,
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Search
     *
     * @return string
     */
    public function actionSearch()
    {
        /**
         * [Базовые определения]
         *
         * @var [type]
         */
        $model = 'Test';

        return $this->render(
            'search',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Generate
     *
     * @return string|\yii\web\Response
     */
    public function actionGenerate()
    {
        $model = new task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/task/generate');
        } else {
            return $this->render(
                'generate',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Creates a new task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Updates an existing task model.
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
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing task model.
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
     * Finds the task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
