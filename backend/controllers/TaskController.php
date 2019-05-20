<?php
namespace backend\controllers;

use ArrayObject;
use common\components\MainFunctions;
use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\TaskUser;
use common\models\WorkStatus;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\Task;
use common\models\Equipment;
use common\models\Operation;
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
            'table',
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
            $dataProvider->query->andWhere(['>=','date',$_GET['start_time']]);
            $dataProvider->query->andWhere(['<','date',$_GET['end_time']]);
        }
        $dataProvider->query->andWhere(['=','workStatusUuid',WorkStatus::COMPLETE]);
        $dataProvider->pagination->pageParam = 'dp1';

        $dataProvider2 = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider2->pagination->pageSize = 25;
        $dataProvider2->query->andWhere(['<>','workStatusUuid',WorkStatus::COMPLETE]);
        if (isset($_GET['start_time'])) {
            $dataProvider2->query->andWhere(['>=','date',$_GET['start_time']]);
            $dataProvider2->query->andWhere(['<','date',$_GET['end_time']]);
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
     */
    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MainFunctions::register('task','Создана задача',
                '<a class="btn btn-default btn-xs">'.$model['taskType']['title'].'</a> '.$model['taskTemplate']['title'].'<br/>'.
                '<a class="btn btn-default btn-xs">'.$model['equipment']['title'].'</a> '.$model['comment']);

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
     * @throws \Throwable
     * @throws StaleObjectException
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

    /**
     * Build tree of equipment
     *
     * @return mixed
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
                            ['title' => $task['taskTemplate']['title'], 'key' => $task['_id'], 'folder' => true];
                        $taskUsers = TaskUser::find()->where(['taskUuid' => $task['uuid']])->all();
                        $user_names='';
                        foreach ($taskUsers as $taskUser) {
                             $user_names.=$taskUser['user']['title'];
                            }
                        $childIdx4 = count($tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children']) - 1;
                        $operations = Operation::find()->where(['taskUuid' => $task['uuid']])->all();
                        foreach ($operations as $operation) {
                            $tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][] =
                                [
                                    'title' => $operation['operationTemplate']['title'],
                                    'key' => $operation['_id'],
                                    'folder' => false,
                                    'types' => '',
                                    'info' => '',
                                    'user' => $user_names,
                                    'status' => $operation['workStatus']['title'],
                                    'startDate' => $operation['startDate'],
                                    'closeDate' => $operation['closeDate']
                                ];
                        }
                    }
                }
            }
        }
        return $this->render('tree', [
            'equipment' => $tree
        ]);
    }
}
