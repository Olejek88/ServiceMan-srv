<?php

namespace backend\controllers;

use backend\models\TaskTemplateEquipmentSearch;
use common\components\Errors;
use common\components\MainFunctions;
use common\models\Equipment;
use common\models\Task;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipment;
use common\models\TaskType;
use common\models\Users;
use Cron\CronExpression;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * TaskTemplateEquipmentController implements the CRUD actions for
 * TaskTemplateEquipment model.
 */
class TaskTemplateEquipmentController extends ZhkhController
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
        parent::actionCreate();

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
        parent::actionUpdate($id);

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
        parent::actionDelete($id);

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
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
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

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCalendarGantt()
    {
        MainFunctions::checkTasks(Users::getCurrentOid());
        $events = [];
        $categories = [];
        $all_task_equipment_count = 0;
        $today = time();
        $equipments = Equipment::find()
            ->all();
        foreach ($equipments as $equipment) {
            $taskTemplateEquipments = TaskTemplateEquipment::find()
                ->where(['equipmentUuid' => $equipment['uuid']])
                ->all();

            $task_equipment_count = 0;
            $tasks = [];
            $user = 'Не назначен';

            foreach ($taskTemplateEquipments as $taskTemplateEquipment) {
                //echo $taskTemplateEquipment['equipment']['title'] . ' - ' . $taskTemplateEquipment['taskTemplate']['title'] . '<br/>';
                $selected_user = $taskTemplateEquipment->getUser();
                if ($selected_user)
                    $user = $selected_user['name'];
                else
                    $user = 'Не назначен';
                /*                try {
                                    $last = new DateTime($taskTemplateEquipment["last_date"]);
                                } catch (Exception $e) {
                                }*/
                $taskTemplateEquipment->formDates();
                $dates = $taskTemplateEquipment->getDates();
                if ($dates) {
                    $count = 0;
                    while ($count < count($dates)) {
                        $start = strtotime($dates[$count]);
                        //$finish = $start + $taskTemplate['taskTemplate']['normative']*3600*10;
                        $finish = $start + 3600 * 24;
                        //$end_date = date("Y-m-d H:i:s", $finish);
                        if ($start-$today<=3600*24*31*13) {
                            $tasks[] = [
                                'title' => $taskTemplateEquipment['taskTemplate']['title'],
                                'start' => $start * 1000,
                                'end' => $finish * 1000,
                                'id' => $taskTemplateEquipment['_id'],
                                'y' => $task_equipment_count,
                                'user' => $user
                            ];
                        }
                        $count++;
                        if ($count > 5) break;
                    }
                    $all_tasks = Task::find()
                        ->select('*')
                        ->where(['equipmentUuid' => $taskTemplateEquipment['equipmentUuid']])
                        ->all();
                    foreach ($all_tasks as $task) {
                        $start = strtotime($task["startDate"]) * 1000;
                        $finish = strtotime($task["endDate"]) * 1000;
                        $tasks[] = [
                            'title' => $taskTemplateEquipment['taskTemplate']['title'],
                            'start' => $start,
                            'end' => $finish,
                            'id' => 0,
                            'completed' => 0,
                            'y' => $task_equipment_count,
                            'user' => $user
                        ];
                    }
                }
                $task_equipment_count++;
            }
            if (count($tasks)) {
                $all_task_equipment_count++;
                $events[] = [
                    'title' => $equipment['title'],
                    'address' => $equipment->getAddress(),
                    'data' => $tasks
                ];
            }
        }
        $max = 0;
        if ($all_task_equipment_count > 0) $max = $all_task_equipment_count - 1;
        //echo json_encode($events);
                return $this->render('calendar-gantt', [
                    'events' => $events,
                    'categories' => $categories,
                    'max' => $max
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

    public function actionMove()
    {
        if (isset($_POST["id"])) {
            $model = TaskTemplateEquipment::find()->where(['_id' => $_POST["id"]])->one();
            if ($model) {
                $search = date("Y-m-d H:i:s", $_POST['start']/1000);
                $replace = date("Y-m-d H:i:s", $_POST['end']/1000);
                $result = str_replace($search,$replace,$model['next_dates']);
                $model["next_dates"] = $result;
                $model->save();
                return '['.$search.'] '.$model["next_dates"];
            }
        }
        return false;
    }
}
