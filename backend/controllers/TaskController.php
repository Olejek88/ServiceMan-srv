<?php

namespace backend\controllers;

use ArrayObject;
use backend\models\TaskSearch;
use common\components\MainFunctions;
use common\models\Defect;
use common\models\Equipment;
use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\Operation;
use common\models\Request;
use common\models\Task;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipment;
use common\models\TaskType;
use common\models\TaskUser;
use common\models\User;
use common\models\Users;
use common\models\WorkStatus;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskController extends ZhkhController
{
    /**
     * Lists all Task models.
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        $dataProvider->query->orderBy('_id DESC');

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

        // TODO task_user
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'startDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'startDate', $_GET['end_time']]);
        }
        $dataProvider->query->andWhere(['=', 'workStatusUuid', WorkStatus::COMPLETE]);
        $dataProvider->pagination->pageParam = 'dp1';

        $dataProvider2 = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider2->pagination->pageSize = 25;
        $dataProvider2->query->andWhere(['<>', 'workStatusUuid', WorkStatus::COMPLETE]);
        if (isset($_GET['start_time'])) {
            $dataProvider2->query->andWhere(['>=', 'startDate', $_GET['start_time']]);
            $dataProvider2->query->andWhere(['<', 'startDate', $_GET['end_time']]);
        }
        $dataProvider2->pagination->pageParam = 'dp2';

        return $this->render(
            'table-user',
            [
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2
            ]
        );
    }

    /**
     * Lists all Task models.
     *
     * @return mixed
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
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'deadlineDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'deadlineDate', $_GET['end_time']]);
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
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MainFunctions::register('task', 'Создана задача',
                '<a class="btn btn-default btn-xs">' . $model['taskTemplate']['taskType']['title'] . '</a> ' . $model['taskTemplate']['title'] . '<br/>' .
                '<a class="btn btn-default btn-xs">' . $model['equipment']['title'] . '</a> ' . $model['comment']);
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
     * Build tree of equipment
     *
     * @return mixed
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
     * @throws InvalidConfigException
     */
    public function actionAddTask()
    {
        $model = new Task();
        if ($model->load(Yii::$app->request->post())) {
            $task = MainFunctions::createTask($model['taskTemplate'], $model->equipmentUuid,
                $model->comment, $model->oid, $_POST['userUuid'], $model);
            if (isset($_POST["defectUuid"]) && $task) {
                $defect = Defect::find()->where(['uuid' => $_POST["defectUuid"]])->one();
                if ($defect) {
                    $defect->taskUuid = $task['uuid'];
                    $defect->save();
                }
            }
            MainFunctions::register('task', 'Создана задача',
                '<a class="btn btn-default btn-xs">' . $model['taskTemplate']['taskType']['title'] . '</a> ' . $model['taskTemplate']['title'] . '<br/>' .
                '<a class="btn btn-default btn-xs">' . $model['equipment']['title'] . '</a> ' . $model['comment']);

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
                    'requestUuid' => $_GET["requestUuid"]]);
            else
                return $this->renderAjax('_add_task', ['model' => $model, 'equipmentUuid' => $_GET["equipmentUuid"]]);
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
                    $taskUser->oid = Users::getOid(Yii::$app->user->identity);
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
}
