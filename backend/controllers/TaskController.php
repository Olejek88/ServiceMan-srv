<?php
namespace backend\controllers;

use ArrayObject;
use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\TaskUser;
use Yii;
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
