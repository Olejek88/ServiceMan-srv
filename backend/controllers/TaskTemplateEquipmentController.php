<?php

namespace backend\controllers;

use app\commands\MainFunctions;
use backend\models\TaskTemplateEquipmentSearch;
use common\components\Errors;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipment;
use common\models\TaskType;
use Cron\CronExpression;
use DateTime;
use Exception;
use Yii;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii2fullcalendar\models\Event;

/**
 * TaskTemplateEquipmentController implements the CRUD actions for
 * TaskTemplateEquipment model.
 */
class TaskTemplateEquipmentController extends Controller
{
    protected $modelClass = TaskTemplateEquipment::class;

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
     * Lists all TaskTemplateEquipment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskTemplateEquipmentSearch();
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
     * Displays a single TaskTemplateEquipment model.
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
     * Creates a new TaskTemplateEquipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaskTemplateEquipment();
        $searchModel = new TaskTemplateEquipmentSearch();
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
     * Updates an existing TaskTemplateEquipment model.
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
     * Deletes an existing TaskTemplateEquipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TaskTemplateEquipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return TaskTemplateEquipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskTemplateEquipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * функция отрабатывает сигнал от дерева редактирования Task
     * POST string $uuid - задачи
     * POST string $param - новое название
     * @return mixed
     */
    public function actionEditTask()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = TaskTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template['title'] = $_POST["param"];
                if ($template->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            }
        } else
            return Errors::WRONG_INPUT_PARAMETERS;
        return Errors::GENERAL_ERROR;
    }

    /**
     * функция отрабатывает сигнал от дерева редактирования TaskTemplate
     * POST string $uuid - задачи
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteTask()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = TaskTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            } else
                return Errors::ERROR_SAVE;
        } else return Errors::WRONG_INPUT_PARAMETERS;
    }

    /**
     * функция отрабатывает сигнал от дерева добавления task
     * POST string $param - id шаблона
     * @return mixed
     */
    public function actionAddTask()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["param"])) {
            $taskType = TaskType::find()->where(['_id' => $_POST["param"]])->one();
            if ($taskType) {
                $model = new TaskTemplate();
                $model->uuid = (new MainFunctions)->GUID();
                $model->title = 'Новый шаблон';
                $model->taskTypeUuid = $taskType['uuid'];
                $model->description = 'Новый шаблон';
                $model->normative = 0;
                if ($model->save()) {
                    $model->refresh();
                    return $model->_id;
                } else
                    return Errors::GENERAL_ERROR;
            }
        } else return Errors::GENERAL_ERROR;
        return Errors::GENERAL_ERROR;
    }

    /**
     * функция отрабатывает сигнал от дерева удаления EquipmentStage
     * POST string $uuid
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteStage()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = TaskTemplateEquipment::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            } else
                return Errors::ERROR_SAVE;
        } else return Errors::WRONG_INPUT_PARAMETERS;
    }

    public function actionCalendar()
    {
        $events = [];
        $taskTemplateEquipments = TaskTemplateEquipment::find()
            ->select('*')
            ->all();
        foreach ($taskTemplateEquipments as $taskTemplateEquipment) {
            // в этой версии периодичность назначается только типовым задачам
/*            $period = $taskTemplateEquipment["period"];
            try {
                $last = new DateTime($taskEquipmentStage["last_date"]);
                $cron = CronExpression::factory($period);
                $first_date = $cron->getPreviousRunDate();
                foreach ($cron->getMultipleRunDates(5, $last, false) as $date) {
                    //echo $last->format('Y-m-d H:i:s') . PHP_EOL;
                    //$diff = date_diff($cron->getNextRunDate(),$cron->getPreviousRunDate(),true);
                    //$next_date = $first_date->add($diff)->format('Y-m-d H:i:s');
                    $event = new Event();
                    $event->id = $taskEquipmentStage['_id'];
                    $event->title = '[' . $taskEquipmentStage['equipmentStage']['equipmentModel']['title'] . '] '
                        . $taskEquipmentStage['equipmentStage']['stageOperation']['stageTemplate']['title'];
                    $event->start = $date->format('Y-m-d H:i:s');
                    $event->backgroundColor = 'gray';
                    //TODO реальная продолжительность задачи
                    //$event->end = $next_date->add(new \DateInterval(3600,))->format('Y-m-d H:i:s')
                    //$event->url = '/task-equipment-stage/' . $taskEquipmentStage['_id'];
                    $event->url = '/stage-template/view?id=' . $taskEquipmentStage['equipmentStage']['stageOperation']['stageTemplate']['_id'];
                    $event->color = '#333333';
                    $events[] = $event;
                }
            } catch (Exception $e) {
                //echo $e;
            }

            $stages = Stage::find()
                ->select('*')
                ->where(['stageTemplateUuid' => $taskEquipmentStage['equipmentStage']['stageOperation']['stageTemplateUuid']])
                ->all();
            foreach ($stages as $stage) {
                $event = new Event();
                $event->id = $taskEquipmentStage['_id'];
                $event->title = '[' . $stage['equipment']['title'] . '] '
                    . $taskEquipmentStage['equipmentStage']['stageOperation']['stageTemplate']['title'];
                $event->start = $stage['startDate'];
                $event->backgroundColor = 'gray';
                if ($stage['stageStatusUuid'] == StageStatus::COMPLETE)
                    $event->backgroundColor = 'green';
                if ($stage['stageStatusUuid'] == StageStatus::UN_COMPLETE)
                    $event->backgroundColor = 'lightred';
                if ($stage['stageStatusUuid'] == StageStatus::IN_WORK)
                    $event->backgroundColor = 'orange';
                $event->url = '/stage-template/view?id=' . $taskEquipmentStage['_id'];
                $event->color = '#333333';
                $events[] = $event;
            }*/
        }

        return $this->render('calendar', [
            'events' => $events
        ]);
    }

    public function actionPeriod()
    {
        if (isset($_GET["taskTemplateEquipmentUuid"])) {
            $model = TaskTemplateEquipment::find()->where(['uuid' => $_GET["taskTemplateEquipmentUuid"]])
                ->one();
            return $this->renderAjax('_change_form', [
                'model' => $model,
            ]);
        }
        if (isset($_POST["TaskTemplateEquipment"]["period"]) || $_POST["TaskTemplateEquipment"]["period"] == "") {
            try {
                CronExpression::factory($_POST["TaskTemplateEquipment"]["period"]);
            } catch (Exception $e) {
                if ($_POST["TaskTemplateEquipment"]["period"] != "")
                    return "Ошибка задания периода";
            }
            $model = TaskTemplateEquipment::find()->where(['_id' => $_POST["TaskTemplateEquipment"]["_id"]])
                ->one();
            if ($model) {
                $model["period"] = $_POST["TaskTemplateEquipment"]["period"];
                $model->save();
            }
        }
        return false;
    }
}
