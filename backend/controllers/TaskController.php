<?php

namespace backend\controllers;

use ArrayObject;
use backend\models\TaskSearch;
use common\components\MainFunctions;
use common\models\Defect;
use common\models\Equipment;
use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\Measure;
use common\models\Operation;
use common\models\Photo;
use common\models\Request;
use common\models\Task;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipment;
use common\models\TaskType;
use common\models\TaskUser;
use common\models\Users;
use common\models\UserSystem;
use common\models\WorkStatus;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii2fullcalendar\models\Event;

class TaskController extends ZhkhController
{
    /**
     * Lists all Task models.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        $dataProvider->query->orderBy('_id DESC');
        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%'.$_GET['address'].'%',false],
                    ['like', 'object.title', '%'.$_GET['address'].'%',false],
                    ['like', 'street.title', '%'.$_GET['address'].'%',false]]
            );
        }
        return $this->render(
            'table-report-view',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Lists all Task models.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTableUser()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Task::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'workStatusUuid') {
                $model['workStatusUuid'] = $_POST['Task'][$_POST['editableIndex']]['workStatusUuid'];
            }
            $model->save();
            return json_encode('');
        }
        $tasks = [];
        $tasks_completed = [];
        $taskUsers = TaskUser::find()->all();
        if (isset($_GET['user']) && $_GET['user']!="")
            $taskUsers = TaskUser::find()->where(['userUuid' => $_GET['user']])->all();
        foreach ($taskUsers as $taskUser) {
            $task = null;
            $task_complete = null;
            if (isset($_GET['start_time'])) {
                $task_complete = Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['IN', 'workStatusUuid', [WorkStatus::COMPLETE, WorkStatus::UN_COMPLETE]])
                    ->andWhere('taskDate > ' . date("YmdHis",strtotime($_GET['start_time'])))
                    ->andWhere('taskDate < ' . date("YmdHis",strtotime($_GET['end_time'])))
                    ->one();
                $task = Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['IN', 'workStatusUuid', [WorkStatus::NEW, WorkStatus::IN_WORK]])
                    ->andWhere('taskDate > ' . date("Ymdhis",strtotime($_GET['start_time'])))
                    ->andWhere('taskDate < ' . date("Ymdhis",strtotime($_GET['end_time'])))
                    ->one();
            }
            else {
                $task_complete = Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['IN', 'workStatusUuid', [WorkStatus::COMPLETE, WorkStatus::UN_COMPLETE]])
                    ->one();
                $task = Task::find()->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['IN', 'workStatusUuid', [WorkStatus::NEW, WorkStatus::IN_WORK]])
                    ->one();
            }
            if ($task)
                $tasks[] = $task;
            if ($task_complete)
                $tasks_completed[] = $task_complete;

        }
        $users = Users::find()
            ->where('name != "sUser"')
            ->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');

        return $this->render(
            'table-user',
            [
                'tasks' => $tasks,
                'tasks_completed' => $tasks_completed,
                'users' => $items
            ]
        );
    }

    /**
     * Lists all Task models.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTableUserNormative()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        $dataProvider->query->andWhere(['=', 'workStatusUuid', WorkStatus::COMPLETE]);

        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'endDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'endDate', $_GET['end_time']]);
        }
        if (isset($_GET['user']) && $_GET['user']!='') {
            $taskUsers = TaskUser::find()->select('taskUuid')->where(['userUuid' => $_GET['user']])->all();
            $list=[];
            foreach ($taskUsers as $taskUser) {
                $list[] = $taskUser['taskUuid'];
            }
            $dataProvider->query->andWhere(['uuid' => $list])->all();
        }
        $dataProvider->query->andWhere(['=', 'workStatusUuid', WorkStatus::COMPLETE]);
        return $this->render(
            'table-user-normative',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel
            ]
        );
    }

    /**
     * Lists all Task models.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTableReportView()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        $taskTemplates = TaskTemplate::find()->select('uuid, taskTypeUuid')->where(['taskTypeUuid' => TaskType::TASK_TYPE_VIEW])->all();
        $list=[];
        foreach ($taskTemplates as $taskTemplate) {
            $list[] = $taskTemplate['uuid'];
        }
        $dataProvider->query->andWhere(['taskTemplateUuid' => $list])->all();

        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'date', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'date', $_GET['end_time']]);
        }
        $dataProvider->query->orderBy('_id DESC');
        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%'.$_GET['address'].'%',false],
                    ['like', 'object.title', '%'.$_GET['address'].'%',false],
                    ['like', 'street.title', '%'.$_GET['address'].'%',false]]
            );
        }
        return $this->render(
            'table-report-view',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'titles' => 'Журнал осмотров'
            ]
        );
    }

    /**
     * Lists all Task models.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Task::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'workStatusUuid') {
                $status = $_POST['Task'][$_POST['editableIndex']]['workStatusUuid'];
                if ($status==WorkStatus::COMPLETE) {
                    $model['startDate'] = $model['taskDate'];
                    $model['endDate'] = date("Y-m-d H:i:s");
                }
                $model['workStatusUuid'] = $_POST['Task'][$_POST['editableIndex']]['workStatusUuid'];
            }
            if ($_POST['editableAttribute'] == 'taskDate') {
                $model['taskDate'] = $_POST['Task'][$_POST['editableIndex']]['taskDate'];
            }
            if ($_POST['editableAttribute'] == 'deadlineDate') {
                $model['deadlineDate'] = $_POST['Task'][$_POST['editableIndex']]['deadlineDate'];
            }

            $model->save();
            return json_encode('');
        }

        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%'.$_GET['address'].'%',false],
                    ['like', 'object.title', '%'.$_GET['address'].'%',false],
                    ['like', 'street.title', '%'.$_GET['address'].'%',false]]
            );
        }
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'deadlineDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'deadlineDate', $_GET['end_time']]);
        }
        if (isset($_GET['type'])) {
            if ($_GET['type'] == '0') {
                $dataProvider->query
                    ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                    ->andWhere(['<=', 'deadlineDate', 'endDate']);
            }
            if ($_GET['type'] == '1') {
                $dataProvider->query
                    ->andWhere(['!=', 'workStatusUuid', WorkStatus::COMPLETE])
                    ->andWhere(['and', ['<=', 'deadlineDate', 'endDate'], ['IS NOT', 'endDate', null]]);
            }
            if ($_GET['type'] == '2') {
                $dataProvider->query
                    ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                    ->andWhere(['and', ['<', 'deadlineDate', 'endDate'], ['IS NOT', 'endDate', null]]);
            }
            if ($_GET['type'] == '3') {
                $dataProvider->query
                    ->andWhere(['workStatusUuid' => WorkStatus::CANCELED]);
            }
        }
        if (isset($_GET['uuid'])) {
            $dataProvider->query->andWhere(['task.uuid' => $_GET['uuid']]);
        }
        $dataProvider->query->orderBy('_id DESC');
        return $this->render(
            'table-report-view',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'titles' => 'Журнал задач'
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
     * Creates a new task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MainFunctions::register('task', 'Создана задача',
                '<a class="btn btn-default btn-xs">' . $model['taskTemplate']['taskType']['title'] . '</a> ' . $model['taskTemplate']['title'] . '<br/>' .
                '<a class="btn btn-default btn-xs">' . $model['equipment']['title'] . '</a> ' . $model['comment'],
                $model->uuid);
            // TODO реализовать логику выбора пользователя
            $user = Users::find()->one();
            $modelTU = new TaskUser();
            $modelTU->uuid = (new MainFunctions)->GUID();
            $modelTU->taskUuid = $model['uuid'];
            $modelTU->userUuid = $user['uuid'];
            $modelTU->oid = Users::getCurrentOid();
            $modelTU->save();
            //echo json_encode($modelTU->errors);
            return self::actionIndex();
        } else {
            $searchModel = new TaskSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 25;

            return $this->render(
                'create',
                [
                    'model' => $model, 'dataProvider' => $dataProvider
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
     * Deletes an existing task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
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

    /**
     * Task report
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionReport()
    {
        $start_date = '2011-01-01 00:00:00';
        $end_date = '2031-01-01 00:00:00';
        if (isset($_GET['start_time'])) {
            $start_date = $_GET['start_time'].' 00:00:00';
            $end_date = $_GET['end_time'].' 00:00:00';
        }

        $users = Users::find()
            ->where('name != "sUser"')
            ->all();
        if (isset($_GET['user_select']) && $_GET['user_select']!='') {
            $users = Users::find()
                ->where(['uuid' => $_GET["user_select"]])
                ->all();
        }
        $user_array = [];
        $t_count = 1;
        $categories = "";

        $bar = "{ name: 'Выполнено', color: 'blue', ";
        $bar .= "data: [";
        $count = 0;
        foreach ($users as $current_user) {
            $taskComplete=0;
            if ($count > 0) {
                $categories .= ',';
                $bar .= ",";
            }
            $categories .= '"' . $current_user['name'] . '"';

            $taskUsers = TaskUser::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskComplete += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['>','taskdate',$start_date])
                    ->andWhere(['<','taskdate',$end_date])
                    ->andWhere(['IN','workStatusUuid',[WorkStatus::COMPLETE,WorkStatus::UN_COMPLETE,WorkStatus::CANCELED]])
                    ->count();
            }
            $bar .= $taskComplete;
            $count++;
        }
        $bar .= "]},";

        $count=0;
        $bar .= "{ name: 'Выполнено в срок', color: 'green', ";
        $bar .= "data: [";
        foreach ($users as $current_user) {
            if ($count > 0) {
                $bar .= ",";
            }

            $userSystems = UserSystem::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();

            foreach ($userSystems as $userSystem) {
                $user_array[$t_count]['count'] = $t_count;
                $user_array[$t_count]['name'] = $current_user['name'];
                $user_array[$t_count]['system'] = $userSystem['equipmentSystem']['title'];

                $taskGood=0;
                $taskBad=0;
                $taskComplete=0;
                $taskTotal=0;

                $taskUsers = TaskUser::find()
                    ->where(['userUuid' => $current_user['uuid']])
                    ->all();
                foreach ($taskUsers as $taskUser) {
                    $tasks = Task::find()
                        ->where(['uuid' => $taskUser['taskUuid']])
                        ->andWhere(['>','taskdate',$start_date])
                        ->andWhere(['<','taskdate',$end_date])
                        ->all();
                    foreach ($tasks as $task) {
                        if ($task['equipment']['equipmentType']['equipmentSystemUuid'] == $userSystem['equipmentSystemUuid']) {
                            $taskTotal++;
                        }
                    }

                    $tasks = Task::find()
                        ->where(['uuid' => $taskUser['taskUuid']])
                        ->andWhere(['>','taskdate',$start_date])
                        ->andWhere(['<','taskdate',$end_date])
                        ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                        ->andWhere('endDate <= deadlineDate')
                        ->all();
                    foreach ($tasks as $task) {
                        if ($task['equipment']['equipmentType']['equipmentSystemUuid'] == $userSystem['equipmentSystemUuid'])
                            $taskGood++;
                    }
                    $tasks = Task::find()
                        ->where(['uuid' => $taskUser['taskUuid']])
                        ->andWhere(['>','taskdate',$start_date])
                        ->andWhere(['<','taskdate',$end_date])
                        ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                        ->all();
                    foreach ($tasks as $task) {
                        if ($task['equipment']['equipmentType']['equipmentSystemUuid'] == $userSystem['equipmentSystemUuid'])
                            $taskComplete++;
                    }

                    $tasks = Task::find()
                        ->where(['uuid' => $taskUser['taskUuid']])
                        ->andWhere(['>','taskdate',$start_date])
                        ->andWhere(['<','taskdate',$end_date])
                        ->andWhere('deadlineDate > NOW()')
                        ->andWhere(['IN', 'workStatusUuid', [
                            WorkStatus::NEW, WorkStatus::IN_WORK, WorkStatus::COMPLETE
                        ]])
                        ->all();
                    foreach ($tasks as $task) {
                        if ($task['equipment']['equipmentType']['equipmentSystemUuid'] == $userSystem['equipmentSystemUuid'])
                            $taskBad++;
                    }
                }
                $user_array[$t_count]['complete_good'] = $taskGood;
                $user_array[$t_count]['bad'] = $taskBad;
                $user_array[$t_count]['complete'] = $taskComplete;
                $user_array[$t_count]['total'] = $taskComplete+$taskBad;
                $t_count++;
            }

            $taskGood=0;
            $taskUsers = TaskUser::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskGood += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['>','taskdate',$start_date])
                    ->andWhere(['<','taskdate',$end_date])
                    ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                    ->andWhere('endDate <= deadlineDate')
                    ->count();
            }
            $bar .= $taskGood;
            $count++;
        }
        $bar .= "]},";

        $count=0;
        $bar .= "{ name: 'Просрочено', color: 'red', ";
        $bar .= "data: [";
        foreach ($users as $current_user) {
            if ($count > 0) {
                $bar .= ",";
            }
            $taskBad=0;
            $taskUsers = TaskUser::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskBad += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['>','taskdate',$start_date])
                    ->andWhere(['<','taskdate',$end_date])
                    ->andWhere('deadlineDate > NOW()')
                    ->andWhere(['IN', 'workStatusUuid', [
                        WorkStatus::NEW, WorkStatus::IN_WORK, WorkStatus::COMPLETE
                    ]])
                    ->count();
            }
            $bar .= $taskBad;
            $count++;
        }
        $bar .= "]}";

        $users = Users::find()
            ->where('name != "sUser"')
            ->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');

        $equipmentSystems = EquipmentSystem::find()->all();
        $items2 = ArrayHelper::map($equipmentSystems, 'uuid', 'title');

        return $this->render(
            'table-report',
            [
                'bar' => $bar,
                'categories' => $categories,
                'users' => $user_array,
                'usersAll' => $items,
                'systemAll' => $items2
            ]
        );
    }

    /**
     * Build tree of equipment
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTree()
    {
        $tree = new ArrayObject();
        $systems = EquipmentSystem::find()->all();
        foreach ($systems as $system) {
            $tree['children'][] = ['title' => $system['title'], 'key' => $system['_id'] . "", 'folder' => true];
            $childIdx = count($tree['children']) - 1;
            $types = EquipmentType::find()->where(['equipmentSystemUuid' => $system['uuid']])->all();
            foreach ($types as $type) {
                $tree['children'][$childIdx]['children'][] =
                    ['title' => $type['title'], 'key' => $type['_id'], 'folder' => true];
                $childIdx2 = count($tree['children'][$childIdx]['children']) - 1;
                $equipments = Equipment::find()->where(['equipmentTypeUuid' => $type['uuid']])->all();
                foreach ($equipments as $equipment) {
                    $tree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                        ['title' => $equipment['title'], 'key' => $equipment['_id'], 'folder' => true];
                    $childIdx3 = count($tree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                    $tasks = Task::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                    foreach ($tasks as $task) {
                        $tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] =
                            ['title' => $task['taskTemplate']['title'], 'key' => $task['_id'], 'folder' => true,
                                'startDate' => $task['startDate'], 'closeDate' => $task['endDate'],
                                'level' => 'task'

                            ];
                        $taskUsers = TaskUser::find()->where(['taskUuid' => $task['uuid']])->all();
                        $user_names = '';
                        foreach ($taskUsers as $taskUser) {
                            $user_names .= $taskUser['user']['name'];
                        }
                        $childIdx4 = count($tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children']) - 1;
                        $operations = Operation::find()->where(['taskUuid' => $task['uuid']])->all();
                        foreach ($operations as $operation) {
                            $tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][] =
                                [
                                    'title' => $operation['operationTemplate']['title'],
                                    'key' => $operation['_id'],
                                    'folder' => false,
                                    'level' => 'operation',
                                    'types' => '',
                                    'info' => '',
                                    'user' => $user_names,
                                    'status' => $operation['workStatus']['title']
                                ];
                        }
                    }
                }
            }
        }
        return $this->render('tree', [
            'fullTree' => $tree
        ]);
    }

    /**
     * Creates a new task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionAddTask()
    {
        $model = new Task();
        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST["requestUuid"])) {
                $request = Request::find()->where(['uuid' => $_POST["requestUuid"]])->one();
                if ($request) {
                    $model->comment = $request['comment'];
                }
            }
            $task = MainFunctions::createTask($model['taskTemplate'], $model->equipmentUuid,
                $model->comment, $model->oid, $_POST['userUuid'], $model);
            if (isset($_POST["defectUuid"]) && $task) {
                $defect = Defect::find()->where(['uuid' => $_POST["defectUuid"]])->one();
                if ($defect) {
                    $defect['taskUuid'] = $task['uuid'];
                    $defect['defectStatus'] = 1;
                    $defect->save();
                }
            }
            if (isset($_POST["requestUuid"]) && $task) {
                $request = Request::find()->where(['uuid' => $_POST["requestUuid"]])->one();
                if ($request) {
                    $request['taskUuid'] = $task['uuid'];
                    $request->save();
                }
            }
            MainFunctions::register('task', 'Создана задача',
                '<a class="btn btn-default btn-xs">' . $model['taskTemplate']['taskType']['title'] . '</a> ' . $model['taskTemplate']['title'] . '<br/>' .
                '<a class="btn btn-default btn-xs">' . $model['equipment']['title'] . '</a> ' . $model['comment'],
                $task->uuid);

            return self::actionIndex();
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
     * @return string
     */
    public function actionForm()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = new Task();
            if (isset($_GET["requestUuid"]))
                return $this->renderAjax('_add_task', ['model' => $model, 'equipmentUuid' => $_GET["equipmentUuid"],
                    'requestUuid' => $_GET["requestUuid"], 'type_uuid' => $_GET["type_uuid"]]);
            else
                return $this->renderAjax('_add_task', ['model' => $model, 'equipmentUuid' => $_GET["equipmentUuid"],
                    'type_uuid' => $_GET["type_uuid"]]);
        }
        return "";
    }

    /**
     * @return string
     */
    public function actionAddPeriodic()
    {
        if (isset($_POST["uuid"]) && isset($_POST["type_uuid"])) {
            $model = new TaskTemplateEquipment();
            return $this->renderAjax('_add_task_periodic', ['model' => $model,
                'type_uuid' => $_POST["type_uuid"],
                'equipmentUuid' => $_POST["uuid"]]);
        }
        return "";
    }

    /**
     * Creates a new Task model.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public
    function actionNew()
    {
        if (isset($_POST['taskUuid']))
            $model = Task::find()->where(['uuid' => $_POST['taskUuid']])->one();
        else
            $model = new Task();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                if (isset($_POST['requestUuid'])) {
                    $request = Request::find()->where(['uuid' => $_POST['requestUuid']])->one();
                    $request['taskUuid'] = $model['uuid'];
                    $request->save();
                    return true;
                }
            }
        }
        return true;
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public
    function actionName()
    {
        if (isset($_POST['userAdd']) && isset($_POST['taskUuid'])) {
            $user = Users::find()->where(['uuid' => $_POST['userAdd']])->one();
            if ($user) {
                self::checkAddUser($_POST['taskUuid'], $_POST['userAdd'], true);
                $taskUserPresent = TaskUser::find()->where(['taskUuid' => $_POST['taskUuid']])
                    ->andWhere(['userUuid' => $user['uuid']])
                    ->count();
                if ($taskUserPresent==0) {
                    $taskUser = new TaskUser();
                    $taskUser->uuid = MainFunctions::GUID();
                    $taskUser->taskUuid = $_POST['taskUuid'];
                    $taskUser->userUuid = $user['uuid'];
                    $taskUser->oid = Users::getCurrentOid();
                    $taskUser->save();
                    //MainFunctions::register('Смена исполнителя', 'К задаче ')
                }
            }
        }
        //foreach ($_POST as $key => $value) {}
        $users = Users::find()->where(['!=','name','sUser'])->all();
        foreach ($users as $user) {
            $id = 'user-'.$user['_id'];
            if (isset($_POST[$id]) && ($_POST[$id]==1 || $_POST[$id]=="1")) {
                self::checkAddUser($_POST['taskUuid'], $user['uuid'], false);
            }
        }
        return true;
    }

    /**
     * Creates a new Task model.
     * @return mixed
     */
    public
    function actionNewPeriodic()
    {
        $model = new TaskTemplateEquipment();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                $model->formDates();
                $model->save();
            }
        }
        return true;
    }

    /**
     * Удаляет все прееданные объекты из дерева
     * @throws Throwable
     */
    function actionRemove()
    {
        if (isset($_POST["level"]) && isset($_POST["selected_node"])) {
            if ($_POST["level"] == 'task')
                self::deleteTask($_POST["selected_node"]);
            if ($_POST["level"] == 'operation')
                self::deleteOperation($_POST["selected_node"]);
            return 1;
        }
        return 0;
    }

    /**
     * @param $id
     * @throws StaleObjectException
     * @throws Throwable
     */
    function deleteTask($id)
    {
        $task = Task::find()
            ->where(['_id' => $id])
            ->one();
        if ($task) {
            $taskUsers = TaskUser::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskUser->delete();
            }
            $operations = Operation::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();
            foreach ($operations as $operation) {
                $operation->delete();
            }
        }
        $task->delete();
    }

    /**
     * @param $id
     * @throws StaleObjectException
     * @throws Throwable
     */
    function deleteOperation($id)
    {
        $operation = Operation::find()->where(['_id' => $id])->one();
        if ($operation)
            $operation->delete();
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionUser()
    {
        if (isset($_GET["taskUuid"]))
            return $this->renderAjax('_add_user', ['taskUuid' => $_GET["taskUuid"]]);
        else
            return self::actionIndex();
    }

    /**
     * Displays a single Task model.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionInfo()
    {
        if (isset($_GET["task"])) {
            $task = Task::find()
                ->where(['uuid' => $_GET["task"]])
                ->one();
            if ($task)
                return $this->renderAjax('_task_info', ['task' => $task]);
        }
    }

    /**
     * @param $taskUuid
     * @param $userUuid
     * @param $add
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function checkAddUser($taskUuid, $userUuid, $add)
    {
        $taskUserPresent = TaskUser::find()->where(['taskUuid' => $taskUuid])
            ->andWhere(['userUuid' => $userUuid])
            ->one();
        if (!$taskUserPresent && $add==true) {
            $taskUser = new TaskUser();
            $taskUser->uuid = MainFunctions::GUID();
            $taskUser->taskUuid = $taskUuid;
            $taskUser->userUuid = $userUuid;
            $taskUser->oid = Users::getCurrentOid();
            $taskUser->save();
            //MainFunctions::register('Смена исполнителя', 'К задаче ')
        } else {
            if ($taskUserPresent && $add == false) {
                $taskUserPresent->delete();
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCalendar()
    {
        $events = [];
        $today = time();
        $equipments = Equipment::find()
            ->all();
        foreach ($equipments as $equipment) {
            $taskTemplateEquipments = TaskTemplateEquipment::find()
                ->where(['equipmentUuid' => $equipment['uuid']])
                ->all();

            $task_equipment_count = 0;

            foreach ($taskTemplateEquipments as $taskTemplateEquipment) {
                $selected_user = $taskTemplateEquipment->getUser();
                if ($selected_user)
                    $user = $selected_user['name'];
                else
                    $user = 'Не назначен';
                $taskTemplateEquipment->formDates();
                $dates = $taskTemplateEquipment->getDates();
                if ($dates) {
                    $count = 0;
                    while ($count < count($dates)) {
                        $start = strtotime($dates[$count]);
                        $finish = $start + 3600 * 24;
                        if ($start-$today<=3600*24*31*13) {
                            $event = new Event();
                            $event->id = $taskTemplateEquipment['_id'];
                            $event->title = '[' . $user . '] ' . $taskTemplateEquipment['taskTemplate']['title'];
                            $event->backgroundColor = '#aaaaaa';
                            $event->start = $dates[$count];
                            //$event->end = $order['closeDate'];
                            $event->color = '#333333';
                            $events[] = $event;
                        }
                        $count++;
                        if ($count > 5) break;
                    }
                    $all_tasks = Task::find()
                        ->select('*')
                        ->where(['equipmentUuid' => $taskTemplateEquipment['equipmentUuid']])
                        ->all();
                    foreach ($all_tasks as $task) {
                        $taskUsers = TaskUser::find()->where(['taskUuid' => $task['uuid']])->all();
                        $user_names = '';
                        foreach ($taskUsers as $taskUser) {
                            $user_names .= $taskUser['user']['name'];
                        }

                        $event = new Event();
                        $event->id = $task['_id'];
                        $event->title = '[' . $user_names . '] ' . $taskTemplateEquipment['taskTemplate']['title'];
                        if ($task['workStatusUuid']==WorkStatus::CANCELED ||
                            $task['workStatusUuid']==WorkStatus::NEW)
                            $event->backgroundColor='gray';
                        if ($task['workStatusUuid']==WorkStatus::IN_WORK)
                            $event->backgroundColor='yellow';
                        if ($task['workStatusUuid']==WorkStatus::UN_COMPLETE)
                            $event->backgroundColor='lightred';
                        if ($task['workStatusUuid']==WorkStatus::COMPLETE)
                            $event->backgroundColor='green';

                        $event->start = $task["startDate"];
                        $event->end = $task["endDate"];
                        $event->color = '#000000';
                        $events[] = $event;
                    }
                }
                $task_equipment_count++;
            }
        }
        return $this->render('calendar', [
            'events' => $events
        ]);
    }

    /**
     * Lists all Defects models for Task.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionDefects()
    {
        $defects = [];
        if (isset($_GET['uuid']) && isset($_GET['date'])) {
            $start = date("Y-m-d 00:00:00", strtotime($_GET['date']));
            $end = date("Y-m-d 23:59:59", strtotime($_GET['date']));
            $defects = Defect::find()
                ->where(['equipmentUuid' => $_GET['uuid']])
                ->andWhere('date >=\'' . $start . '\'')
                ->andWhere('date <\'' . $end . '\'')
                ->all();
        }
        return $this->renderAjax('_list_defect', [
            'defects' => $defects
        ]);
    }

    /**
     * Lists all Measures models for Task.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionMeasures()
    {
        $measures = [];
        if (isset($_GET['uuid']) && isset($_GET['date'])) {
            $start = date("Y-m-d 00:00:00", strtotime($_GET['date']));
            $end = date("Y-m-d 23:59:59", strtotime($_GET['date']));
            $measures = Measure::find()
                ->where(['equipmentUuid' => $_GET['uuid']])
                ->andWhere('date >=\'' . $start . '\'')
                ->andWhere('date <\'' . $end . '\'')
                ->all();
        }
        return $this->renderAjax('_list_measure', [
            'measures' => $measures
        ]);
    }

    /**
     * Lists all Photos for Task.
     * @return mixed
     */
    public function actionPhotos()
    {
        $photos = [];
        if ($_GET['uuid']) {
            $photos = Photo::find()
                ->where(['objectUuid' => $_GET['uuid']])
                ->all();
        }
        return $this->renderAjax('_list_photo', [
            'photos' => $photos
        ]);
    }
}
