<?php

namespace backend\controllers;

use common\components\Errors;
use common\components\FancyTreeHelper;
use common\components\MainFunctions;
use common\models\ActionType;
use common\models\Equipment;
use common\models\EquipmentModel;
use common\models\EquipmentStage;
use common\models\EquipmentType;
use common\models\EquipmentTypeTree;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\StageOperation;
use common\models\TaskEquipmentStage;
use common\models\TaskStatus;
use common\models\TaskTemplate;
use common\models\TaskType;
use common\models\TaskVerdict;
use Yii;
use backend\models\TaskSearch;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\Task;
use common\models\Stage;
use common\models\Operation;
use common\models\StageStatus;
use common\models\StageVerdict;
use common\models\OperationStatus;
use common\models\OperationVerdict;
use common\models\OperationTemplate;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
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

    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionHelp()
    {
        $actionTypeCount = ActionType::find()->count();
        return $this->render('help', [
            'actionTypeCount' => $actionTypeCount
        ]);
    }

    /**
     * Displays a single Task model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $task = Task::find()
            ->select('orderUuid, taskVerdictUuid, taskStatusUuid, taskTemplateUuid')
            ->where(['_id' => $id])
            ->one();

        $taskTree = array();
        $taskTree[0]['title'] = $task['taskTemplate']->title;
        $taskTree[0]['folder'] = true;
        $taskTree[0]['expanded'] = true;
        $taskTree[0]['key'] = 'none';
        $taskEquipmentStages = TaskEquipmentStage::find()
            ->where(['taskTemplateUuid' => $task['taskTemplate']->uuid])->all();
        $stageCount = 0;
        foreach ($taskEquipmentStages as $taskEquipmentStage) {
            $taskTree[0]['children'][$stageCount]['title'] =
                $taskEquipmentStage['taskTemplate']->title . ' - ' . $taskEquipmentStage['equipmentStage']['equipment']->title;
            $taskTree[0]['children'][$stageCount]['key'] = $taskEquipmentStage['_id'];
            $taskTree[0]['children'][$stageCount]['folder'] = true;
            $taskTree[0]['children'][$stageCount]['expanded'] = true;
            // taskTemplate
            // equipmentStage
            // task_equipment_stage -> equipmentStageUuid -> equipmentStage
            //$equipmentStages = EquipmentStage::find()
            //    ->where(['taskTemplateUuid' => $task['taskTemplate']->uuid])->all();
            //foreach ($equipmentStages as $equipmentStage) {
            $stageOperations = StageOperation::find()
                ->where(['stageTemplateUuid' => $taskEquipmentStage['equipmentStage']['stageOperation']->stageTemplateUuid])
                ->all();
            $operationCount = 0;
            foreach ($stageOperations as $stageOperation) {
                $taskTree[0]['children'][$stageCount]['children'][$operationCount]['title'] =
                    $stageOperation['operationTemplate']->title;
                $taskTree[0]['children'][$stageCount]['children'][$operationCount]['folder'] = false;
                $taskTree[0]['children'][$stageCount]['children'][$operationCount]['expanded'] = false;
                $taskTree[0]['children'][$stageCount]['children'][$operationCount]['key'] =
                    $stageOperation['operationTemplate']->_id;
                $operationCount++;
            }
            $stageCount++;
        }

        return $this->render(
            'view',
            [
                'taskTree' => $taskTree,
                'model' => $this->findModel($id),
            ]
        );
    }

    public function actionInfo($id)
    {
        $model = $this->findModel($id);
//        $task      = Task::find()
//                            ->select('uuid, orderUuid, equipmentUuid, taskVerdictUuid, taskStatusUuid, taskTemplateUuid')
//                            ->where(['_id' => $id])
//                            ->asArray()
//                            ->one();

        /**
         * Выборка задач, этапов и операций для определенного наряда
         */
        $operations = [];

        $stageStatus = StageStatus::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $stageVerdict = StageVerdict::find()
            ->select('uuid, title')
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

        foreach ($model->stages as $key => $stage) {
            $stageIndex[] = Stage::find()
                ->where(['taskUuid' => $stage['uuid']])
                ->asArray()
                ->all();

            foreach ($stageStatus as $status) {
                if ($stage['stageStatusUuid'] === $status['uuid']) {
                    $stages[$key]['stageStatusUuid'] = $status['title'];
                }
            }

            foreach ($stageVerdict as $verdict) {
                if ($stage['stageVerdictUuid'] === $verdict['uuid']) {
                    $stages[$key]['stageVerdictUuid'] = $verdict['title'];
                }
            }
        }

        foreach ($model->stages as $key => $stage) {
            $operations[] = Operation::find()
                ->where(['stageUuid' => $stage['uuid']])
                ->asArray()
                ->all();
        }

        $operationIndex = 0;

        foreach ($operations as $operation) {
            foreach ($operation as $key => $value) {
                $key = $operationIndex;
                $operationIndex = $key + 1;
            }
        }

        foreach ($operations as $index => $operation) {
            foreach ($operation as $key => $value) {

                foreach ($operationTemp as $template) {
                    if ($value['operationTemplateUuid'] === $template['uuid']) {
                        $operations[$index][$key]['operationTemplateUuid'] = $template['title'];
                    }
                }

                foreach ($operationStatus as $status) {
                    if ($value['operationStatusUuid'] === $status['uuid']) {
                        $operations[$index][$key]['operationStatusUuid'] = $status['title'];
                    }
                }

                foreach ($operationVerdict as $verdict) {
                    if ($value['operationVerdictUuid'] === $verdict['uuid']) {
                        $operations[$index][$key]['operationVerdictUuid'] = $verdict['title'];
                    }
                }
            }
        }


        return $this->render(
            'info',
            [
                'stages' => $model->stages,
                'operationIndex' => $operationIndex,
                'operations' => $operations,
                'model' => $model,
                'order' => $model->order
            ]
        );
    }

    public function actionSearch()
    {
        /**
         * [Базовые определения]
         * @var [type]
         */
        $model = 'Test';

        return $this->render('search', [
            'model' => $model,
        ]);
    }

    public function actionGenerate()
    {
        $model = new Task();

        $model->prevCode = 0;
        $model->nextCode = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/stage/generate');
        } else {
            return $this->render('generate', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $taskEquipmentStages = TaskEquipmentStage::find()
                ->where(['taskTemplateUuid' => $model['taskTemplate']->uuid])->all();
            // taskTemplate
            // equipmentStage
            foreach ($taskEquipmentStages as $taskEquipmentStage) {
                $equipmentStages = EquipmentStage::find()
                    ->where(['equipmentUuid' => $taskEquipmentStage['equipmentStage']['equipmentUuid']])
                    ->all();
                // stageOperation
                foreach ($equipmentStages as $equipmentStage) {
                    $stageOperations = StageOperation::find()
                        ->where(['uuid' => $equipmentStage['stageOperationUuid']])
                        ->all();
                    // stageTemplate
                    // operationTemplate
                }
            }

            if (isset($_GET['from']))
                return $this->redirect([$_GET['from']]);
            else
                return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
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
     *
     * @param array $tree Массив в котором нужно изменить индексы
     * @param ActiveRecord|string $modelClass Класс модели
     * @param ActiveRecord|string $entityClass Класс сущности
     * @param ActiveRecord|string $entityInClass Класс сущности
     * @param string $linkField Поле через которое связывается
     *
     * @return mixed
     */
    public function addEquipmentToTree($tree, $modelClass, $entityClass, $entityInClass, $linkField)
    {
        if (is_array($tree)) {
            $tree = array_slice($tree, 0);
            foreach ($tree AS $key => $value) {
                if (is_array($value)) {
                    $tree[$key] = self::addEquipmentToTree(
                        $value, $modelClass, $entityClass, $entityInClass, $linkField
                    );
                }
            }
        }

        if (isset($tree['key'])) {
            $type = EquipmentType::findOne($tree['key']);
            $models = $modelClass::find()->where(['equipmentTypeUuid' => $type['uuid']])->all();
            foreach ($models as $model) {
                $tree['children'][] = ['title' => $model['title'], 'key' => $model['_id'] . "", 'folder' => true];
                $childIdx = count($tree['children']) - 1;
                $equipments = $entityClass::find()->where(['equipmentModelUuid' => $model['uuid']])->all();
                foreach ($equipments as $equipment) {
                    $tree['children'][$childIdx]['children'][] =
                        ['title' => $equipment['title'], 'key' => $equipment['_id'], 'folder' => true];
                    $childIdx2 = count($tree['children'][$childIdx]['children']) - 1;
                    $taskTypes = TaskType::find()->orderBy('title')->all();
                    foreach ($taskTypes as $taskType) {
                        //$equipmentStages = $entityInClass::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                        $equipmentStages = TaskEquipmentStage::find()->groupBy('taskTemplateUuid')
                        //select('distinct(taskTemplateUuid),_id,uuid,equipmentStageUuid')->groupBy('taskTemplateUuid')
                            ->all();
                        foreach ($equipmentStages as $equipmentStage) {
                            if ($equipmentStage['equipmentStage']->equipmentUuid == $equipment['uuid'] &&
                                $equipmentStage['taskTemplate']->taskTypeUuid == $taskType['uuid']
                            ) {
                                $tree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                                    ['title' => $taskType['title'], 'key' => $taskType['_id'], 'folder' => true];
                                $childIdx3 = count($tree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                                $tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] =
                                    ['title' => $equipmentStage['taskTemplate']['title'],
                                        'key' => $equipmentStage['taskTemplate']['_id'],
                                        'param' => $equipment['uuid']];
                            }
                        }
                    }
                }
            }
        }
        return ($tree);
    }

    /**
     * Tree of equipment and stage operation templates
     *
     * @return mixed
     */
    public function actionTree()
    {
        $indexTable = array();
        $typesTree = EquipmentTypeTree::find()
            ->from([EquipmentTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                EquipmentType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->orderBy('title')
            ->all();

        FancyTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render(
                'tree',
                [
                    'ordersTree' => null,
                    'equipmentTree' => null,
                    'taskEquipmentStageTree' => null
                ]
                );
        }

        $types = EquipmentType::find()->indexBy('_id')->orderBy('title')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $tree[] = [
                'title' => $types[$node_id]->title,
                'key' => $node_id,
                'folder' => true,
                'expanded' => false,
                'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }
        unset($indexTable);
        unset($types);
        $equipmentTree = self::addEquipmentToTree($tree,
            EquipmentModel::class,
            Equipment::class,
            EquipmentStage::class,
            'equipmentModelUuid'
        );

        $taskEquipmentStageCount = 0;
        $taskEquipmentStageTree = array();
        $tasks = TaskEquipmentStage::find()->orderBy('createdAt DESC')->all();
        $taskEquipmentStageTree[0]['title'] = 'Все задачи';
        $taskEquipmentStageTree[0]['folder'] = true;
        $taskEquipmentStageTree[0]['expanded'] = true;
        $taskEquipmentStageTree[0]['key'] = 'none';
        foreach ($tasks as $task) {
            $taskEquipmentStageTree[0]['children'][$taskEquipmentStageCount]['title'] = $task['taskTemplate']->title;
            $taskEquipmentStageTree[0]['children'][$taskEquipmentStageCount]['key'] = $task['_id'];
            $taskEquipmentStageTree[0]['children'][$taskEquipmentStageCount]['folder'] = false;
            $taskEquipmentStageTree[0]['children'][$taskEquipmentStageCount]['expanded'] = false;
        }


        $orderStatusCount = 0;
        $ordersTree = array();
        $allStatus = OrderStatus::find()->all();
        $ordersTree[] = ['title' => 'Все наряды', 'folder' => true, 'expanded' => true, 'key' => 'none'];
        foreach ($allStatus as $status) {
            $orders = Orders::find()->where(['orderStatusUuid' => $status['uuid']])->all();
            $ordersTree[0]['children'][] =
                ['title' => $status['title'], 'folder' => true, 'expanded' => true, 'key' => $status['_id']];
            $ordersCount = 0;
            foreach ($orders as $order) {
                $ordersTree[0]['children'][$orderStatusCount]['children'][] =
                    ['title' => $order['title'], 'folder' => true, 'expanded' => true, 'key' => $order['_id'], 'order' => true];
                $tasks = Task::find()->where(['orderUuid' => $order['uuid']])->all();
                foreach ($tasks as $task) {
                    $ordersTree[0]['children'][$orderStatusCount]['children'][$ordersCount]['children'][] =
                        ['title' => $task['taskTemplate']->title.' ['.$task['comment'].']', 'folder' => false, 'key' => $task['_id']];
                }
                $ordersCount++;
            }
            $orderStatusCount++;
        }

        return $this->render(
            'tree', [
                'ordersTree' => $ordersTree,
                'equipmentTree' => $equipmentTree,
                'taskEquipmentStageTree' => $taskEquipmentStageTree
            ]
        );
    }

    /**
     * функция отрабатывает сигнал от дерева выбор task
     * POST string $uuid - task
     * @return mixed
     */

    public function actionCheckTask()
    {
        $this->enableCsrfValidation = false;
        $taskEquipmentStageTree[0]['title'] = 'Задачи для наряда';
        $taskEquipmentStageTree[0]['folder'] = true;
        $taskEquipmentStageTree[0]['expanded'] = true;
        $taskEquipmentStageTree[0]['key'] = 'none';

        if (isset($_POST['uuid'])) {
            $taskTemplate = TaskTemplate::find()->where(['_id' => $_POST['uuid']])->one();
            if ($taskTemplate) {
                $taskEquipmentStageTree[0]['children'][] =
                    ['title' => $taskTemplate['title'], 'key' => $taskTemplate['_id'], 'folder' => true, 'expanded' => true];
                $equipmentStages = TaskEquipmentStage::find()->
                where(['taskTemplateUuid' => $taskTemplate['uuid']])->all();
                foreach ($equipmentStages as $equipmentStage) {
                    $taskEquipmentStageTree[0]['children'][0]['children'][] =
                        ['title' => $equipmentStage['equipmentStage']['stageOperation']['stageTemplate']['title'] . ' :: ' .
                            $equipmentStage['equipmentStage']['stageOperation']['operationTemplate']['title'],
                            'key' => $equipmentStage['_id']];
                }
                return json_encode($taskEquipmentStageTree);
            }
        } else {
            return Errors::WRONG_INPUT_PARAMETERS;
        }

        return Errors::GENERAL_ERROR;
    }

    /**
     * функция отрабатывает сигнал от дерева редактирования TaskTemplate
     * POST string $uuid - задачи
     * @return mixed
     */
    public function actionDeleteTask()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST['uuid'])) {
            $template = TaskEquipmentStage::find()->where(['_id' => $_POST['uuid']])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            } else {
                return Errors::ERROR_SAVE;
            }
        } else {
            return Errors::WRONG_INPUT_PARAMETERS;
        }
    }

    /**
     * функция отрабатывает сигнал перемещения шаблона в используемые
     * POST string $uuid - шаблона
     * POST string $param - uuid оборудования
     * @return mixed
     */
    public function actionMoveTask()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST['taskTemplate']) && isset($_POST['uuidTemplate'])
            && isset($_POST['uuidTask']) && isset($_POST['orderUuid'])
            && isset($_POST['currentTS'])) {
            // переносим все taskTemplate
            if ($_POST['taskTemplate'] == '1') {
                $order = Orders::find()->where(['_id' => $_POST['orderUuid']])->one();
                $taskTemplate = TaskTemplate::find()->where(['_id' => $_POST['uuidTemplate']])->one();
                if ($taskTemplate && $order) {
                    $taskEquipmentStages = TaskEquipmentStage::find()->where(['taskTemplateUuid' =>
                        $taskTemplate['uuid']])->all();
                    if ($taskEquipmentStages) {
                        // проверяем, что задача с таким темплейтом уже есть для этого наряда - тогда нет смысла ее
                        // создавать еще раз
                        $task = Task::find()
                            ->where(['orderUuid' => $order['uuid']])
                            ->andWhere(['taskTemplateUuid' => $taskTemplate['uuid']])
                            ->one();
                        if (!$task) {
                            $model = new Task();
                            $model->uuid = (new MainFunctions)->GUID();
                            $model->taskTemplateUuid = $taskTemplate['uuid'];
                            //$model->equipmentUuid = $taskEquipmentStages[0]['equipmentStage']['equipment']->uuid;
                            $model->nextCode = 0;
                            $model->prevCode = 0;
                            $model->comment='Задача создана вручную';
                            $model->taskVerdictUuid = TaskVerdict::INSPECTED;
                            $model->taskStatusUuid = TaskStatus::NEW_TASK;
                            $model->orderUuid = $order['uuid'];
                            if (!$model->save())
                                return Errors::ERROR_SAVE;
                                //return 'Task:'.json_encode($model->errors);
                            $taskUuid = $model->uuid;
                        } else {
                            $taskUuid = $task['uuid'];
                        }
                        foreach ($taskEquipmentStages as $taskEquipmentStage) {
                            $stage = Stage::find()
                                ->where(['stageTemplateUuid' => $taskEquipmentStage['equipmentStage']['stageOperation']['stageTemplateUuid']])
                                ->andWhere(['taskUuid' => $taskUuid])
                                ->one();
                            if (!$stage) {
                                $model = new Stage();
                                $model->uuid = (new MainFunctions)->GUID();
                                $model->stageTemplateUuid = $taskEquipmentStage['equipmentStage']['stageOperation']['stageTemplateUuid'];
                                $model->equipmentUuid = $taskEquipmentStage['equipmentStage']['equipment']->uuid;
                                $model->comment = 'Этап создан вручную';
                                $model->flowOrder = 0;
                                $model->stageStatusUuid = StageStatus::NEW_TASK;
                                $model->stageVerdictUuid = StageVerdict::NO_INSPECTED;
                                $model->taskUuid = $taskUuid;
                                if (!$model->save())
                                    //return 'Stage:'.json_encode($model->errors);
                                    return Errors::ERROR_SAVE;
                                $stageUuid = $model->uuid;

                                $model = new Operation();
                                $model->uuid = (new MainFunctions)->GUID();
                                $model->operationTemplateUuid = $taskEquipmentStage['equipmentStage']['stageOperation']['operationTemplateUuid'];
                                $model->flowOrder = 0;
                                $model->comment='Этап создан вручную';
                                $model->operationStatusUuid = OperationStatus::NEW_OPERATION;
                                $model->operationVerdictUuid = OperationVerdict::NO_VERDICT;
                                $model->stageUuid = $stageUuid;
                                if (!$model->save())
                                    //return 'Operation:'.json_encode($model->errors);
                                    return Errors::ERROR_SAVE;
                            }
                        }
                        return Errors::OK;
                    }
                }
            }
            // переносим только одну запись этап-операция
            if ($_POST['taskTemplate'] == '0') {
                $taskEquipmentStage = TaskEquipmentStage::find()->where(['_id' =>
                    $_POST['uuidTask']])->one();
            }
        } else return Errors::WRONG_INPUT_PARAMETERS;
        return Errors::GENERAL_ERROR;
    }

    /**
     * функция отрабатывает сигнал от дерева удаления
     * POST string $uuid
     * @return mixed
     */
    public function actionDeleteTaskStage()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST['uuid'])) {
            $template = TaskEquipmentStage::find()->where(['_id' => $_POST['uuid']])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            } else {
                return Errors::ERROR_SAVE;
            }
        } else {
            return Errors::WRONG_INPUT_PARAMETERS;
        }
    }
}
