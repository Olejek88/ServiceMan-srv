<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\Orders;
use common\models\Task;
use common\models\Stage;
use common\models\Equipment;
use common\models\StageStatus;
use common\models\StageVerdict;
use common\models\StageTemplate;
use common\models\Operation;
use common\models\OperationTemplate;
use common\models\OperationStatus;
use common\models\OperationVerdict;

use backend\models\StageSearch;

/**
 * StageController implements the CRUD actions for Stage model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class StageController extends Controller
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
                'class' => VerbFilter::className(),
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
     * Lists all Stage models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StageSearch();
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
     * Displays a single Stage model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = Stage::findOne($id);
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
        $stage = Stage::find()
            ->select('uuid, taskUuid, equipmentUuid, stageStatusUuid, stageVerdictUuid, stageTemplateUuid')
            ->where(['_id' => $id])
            ->asArray()
            ->one();

        $task = Task::find()
            ->select('_id, comment, orderUuid')
            ->where(['uuid' => $stage['taskUuid']])
            ->asArray()
            ->one();

        $equipment = Equipment::find()
            ->select('title')
            ->where(['uuid' => $stage['equipmentUuid']])
            ->asArray()
            ->one();

        $statusTitle = StageStatus::find()
            ->select('title')
            ->where(['uuid' => $stage['stageStatusUuid']])
            ->asArray()
            ->one();

        $verdictTitle = StageVerdict::find()
            ->select('title')
            ->where(['uuid' => $stage['stageVerdictUuid']])
            ->asArray()
            ->one();

        $templateTitle = StageTemplate::find()
            ->select('title')
            ->where(['uuid' => $stage['stageTemplateUuid']])
            ->asArray()
            ->one();

        /**
         * Выборка задач, этапов и операций для определенного наряда
         */
        $operationsFind = Operation::find()
            ->where(['stageUuid' => $stage['uuid']])
            ->asArray()
            ->all();

        $operationTemp = OperationTemplate::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $operationStatus = OperationStatus::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $operationVerdict = OperationVerdict::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        foreach ($operationsFind as $key => $operation) {
            foreach ($operationTemp as $template) {
                if ($operation['operationTemplateUuid'] === $template['uuid']) {
                    $operationsFind[$key]['operationTemplateUuid'] = $template['title'];
                }
            }

            foreach ($operationStatus as $status) {
                if ($operation['operationStatusUuid'] === $status['uuid']) {
                    $operationsFind[$key]['operationStatusUuid'] = $status['title'];
                }
            }

            foreach ($operationVerdict as $verdict) {
                if ($operation['operationVerdictUuid'] === $verdict['uuid']) {
                    $operationsFind[$key]['operationVerdictUuid'] = $verdict['title'];
                }
            }
        }

        $order = Orders::find()
            ->select('_id, title')
            ->where(['uuid' => $task['orderUuid']])
            ->asArray()
            ->one();

        return $this->render(
            'info',
            [
                'order' => $order,
                'task' => $task,
                'equipment' => $equipment,
                'status' => $statusTitle,
                'verdict' => $verdictTitle,
                'template' => $templateTitle,
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
        $model = new Stage();

        $model->flowOrder = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/operation/generate');
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
     * Creates a new Stage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stage();

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
     * Updates an existing Stage model.
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
     * Deletes an existing Stage model.
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
     * Finds the Stage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Stage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
