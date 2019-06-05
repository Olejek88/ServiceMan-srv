<?php

namespace backend\controllers;

use backend\models\TaskSearchTemplate;
use backend\models\TaskSearchType;
use common\components\MainFunctions;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\OperationTemplate;
use common\models\TaskOperation;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipment;
use Yii;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TaskTemplateController implements the CRUD actions for TaskTemplate model.
 */
class TaskTemplateController extends Controller
{
    protected $modelClass = TaskTemplate::class;

    /**
     * Lists all TaskTemplate models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearchTemplate();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single TaskTemplate model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render(
            'view',
            [
                'model' => $model,
                'type' => $model->taskType,
            ]
        );
    }

    /**
     * Creates a new TaskTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaskTemplate();
        $searchModel = new TaskSearchTemplate();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                    'dataProvider' => $dataProvider
                ]
            );
        }
    }

    /**
     * Updates an existing TaskTemplate model.
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
     * Deletes an existing TaskTemplate model.
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
     * Finds the TaskTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return TaskTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskTemplate::findOne($id)) !== null) {
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
        $tree = array();
        $fullTree2 = self::addEquipmentStageToTree($tree,
            EquipmentType::class,
            Equipment::class,
            'equipmentTypeUuid'
        );
        return $this->render('tree', [
            'equipment' => $fullTree2
        ]);
    }

    /**
     *
     * @param array $tree Массив в котором нужно изменить индексы
     * @param ActiveRecord|string $modelClass Класс модели
     * @param ActiveRecord|string $entityClass Класс сущности
     * @param string $linkField Поле через которое связывается
     *
     * @return mixed
     */
    public
    static function addEquipmentStageToTree($tree, $modelClass, $entityClass, $linkField)
    {
        $types = EquipmentType::find()->all();
        foreach ($types as $type) {
            $expanded = true;
            $tree['children'][] = ['title' => $type['title'], 'key' => $type['_id'] . "",
                'expanded' => $expanded, 'folder' => true, 'type' => true, 'type_id' => $type['_id'] . "",
                'operation' => false];
            $childIdx = count($tree['children']) - 1;

            $equipments = Equipment::find()->where(['equipmentTypeUuid' => $type['uuid']])->all();
            foreach ($equipments as $equipment) {
                $tree['children'][$childIdx]['children'][] = [
                    'title' => $equipment->getFullTitle(),
                    'key' => $equipment['_id'] . "",
                    'expanded' => $expanded,
                    'folder' => true,
                    'equipment' => false,
                    'equipment_id' => $equipment['_id'] . "",
                    'operation' => false];
                $childIdx2 = count($tree['children'][$childIdx]['children']) - 1;

                $taskTemplateEquipments = TaskTemplateEquipment::find()
                    ->where(['equipmentUuid' => $equipment['uuid']])
                    ->all();
                foreach ($taskTemplateEquipments as $taskTemplateEquipment) {
                    $period_text = $taskTemplateEquipment["period"];
                    if ($taskTemplateEquipment["period"] == "@hourly")
                        $period_text = "каждый час";
                    if ($taskTemplateEquipment["period"] == "@daily")
                        $period_text = "каждый день";
                    if ($taskTemplateEquipment["period"] == "@yearly")
                        $period_text = "раз в год";
                    if ($taskTemplateEquipment["period"] == "@monthly")
                        $period_text = "каждый месяц";
                    $period = '<div class="progress"><div class="critical4">' .
                        $period_text . '</div></div>';
                    if ($taskTemplateEquipment["period"] == "") {
                        $period = '<div class="progress"><div class="critical5">не задан</div></div>';
                    }
                    $period = Html::a($period,
                        ['/task-template-equipment/period', 'taskTemplateEquipmentUuid' => $taskTemplateEquipment['uuid']],
                        [
                            'title' => 'Задать период',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalStatus',
                        ]
                    );
                    $type = '<div class="progress"><div class="critical5">' .
                        $taskTemplateEquipment["taskTemplate"]["title"] . '</div></div>';
                    $tree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                        ['key' => $taskTemplateEquipment["taskTemplate"]["_id"] . "",
                            'folder' => false,
                            'task_id' => $taskTemplateEquipment["taskTemplate"]['_id'],
                            'task' => false,
                            'uuid' => $taskTemplateEquipment["taskTemplate"]["uuid"],
                            'operation' => false,
                            'created' => $taskTemplateEquipment["taskTemplate"]["changedAt"],
                            'description' => $taskTemplateEquipment["taskTemplate"]["description"],
                            'types' => $type,
                            'expanded' => true,
                            'period' => $period,
                            'task_template_equipment' => $taskTemplateEquipment['_id'],
                            'normative' => $taskTemplateEquipment["taskTemplate"]["normative"],
                            'title' => mb_convert_encoding($taskTemplateEquipment["taskTemplate"]["title"], 'UTF-8', 'UTF-8'),
                        ];


                    $childIdx3 = count($tree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                    $taskOperations = TaskOperation::find()
                        ->where(['taskTemplateUuid' => $taskTemplateEquipment["taskTemplate"]["uuid"]])
                        ->all();
                    foreach ($taskOperations as $taskOperation) {
                        $type = '<div class="progress"><div class="critical5">' .
                            $taskOperation["operationTemplate"]["title"] . '</div></div>';
                        $tree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] =
                            ['key' => $taskOperation["operationTemplate"]["_id"] . "",
                                'folder' => false,
                                'expanded' => true,
                                'created' => $taskOperation["operationTemplate"]["changedAt"],
                                'types' => $type,
                                'uuid' => $taskOperation["operationTemplate"]["uuid"],
                                'normative' => '-',
                                'description' => mb_convert_encoding(substr($taskOperation["operationTemplate"]["description"],
                                    0, 150), 'UTF-8', 'UTF-8'),
                                'model' => false,
                                'operation' => true,
                                'task_operation_id' => $taskOperation['_id'],
                                'operation_id' => $taskOperation["operationTemplate"]["_id"],
                                'title' => mb_convert_encoding($taskOperation["operationTemplate"]["title"], 'UTF-8', 'UTF-8'),
                            ];
                    }
                }
            }
        }
        return ($tree);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового шаблона этапа или операции
     *
     * @return mixed
     */
    public function actionAdd()
    {
        MainFunctions::log("tree.log", "[add] stage template / model" . $_POST["selected_node"]);
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            $type_id = 0;
            $equipment_id = 0;
            $task_id = 0;
            if (isset($_POST["type_id"]))
                $type_id = $_POST["type_id"];
            if (isset($_POST["equipment_id"]))
                $equipment_id = $_POST["equipment_id"];
            if (isset($_POST["task_id"]))
                $task_id = $_POST["task_id"];

            // тип оборудования
            if ($folder == "true" && $type_id > 0) {
                $equipment = new Equipment();
                return $this->renderAjax('../equipment/_add_form', [
                    'type_id' => $type_id,
                    'equipment' => $equipment
                ]);
            }

            // оборудование
            if ($folder == "true" && $equipment_id > 0) {
                $taskTemplate = new TaskTemplate();
                $taskTemplateEquipment = new TaskTemplateEquipment();
                return $this->renderAjax('_add_task', [
                    'equipment_id' => $equipment_id,
                    'taskTemplate' => $taskTemplate,
                    'taskTemplateEquipment' => $taskTemplateEquipment,
                ]);
            }

            // задача
            if ($folder == "false" && $task_id > 0) {
                $taskTemplate = TaskTemplate::find()->where(['_id' => $task_id])->one();
                if ($taskTemplate) {
                    $taskTemplateEquipment = TaskTemplateEquipment::find()
                        ->where(['equipmentUuid' => $equipment_id])
                        ->andWhere(['taskTemplateUuid' => $taskTemplate['uuid']])
                        ->one();
                    $operationTemplate = new OperationTemplate();
                    MainFunctions::log("tree.log", "!operationTemplate");
                    return $this->renderAjax('_add_operation', [
                        'taskTemplateUuid' => $taskTemplate['uuid'],
                        'taskTemplateEquipment' => $taskTemplateEquipment['uuid'],
                        'equipment_uuid' => $taskTemplateEquipment['equipmentUuid'],
                        'operationTemplate' => $operationTemplate
                    ]);
                }
            }
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет удаление выбранного шаблона и всех операций
     * @return mixed
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public
    function actionRemove()
    {
        if (isset($_POST["selected_node"])) {
            $node = $_POST["selected_node"];
            $folder = $_POST["folder"];
            $type_id = 0;
            $equipment_id = 0;
            $task_operation_id = 0;
            $task_template_equipment = 0;
            if (isset($_POST["type_id"]))
                $type_id = $_POST["type_id"];
            if (isset($_POST["equipment_id"]))
                $equipment_id = $_POST["equipment_id"];
            if (isset($_POST["task_operation_id"]))
                $task_operation_id = $_POST["task_operation_id"];
            if (isset($_POST["task_template_equipment"]))
                $task_template_equipment = $_POST["task_template_equipment"];

            // тип оборудования
            if ($folder == "true" && $type_id > 0) {
                // тип оборудования удалять не стоит
            }

            // оборудование
            if ($folder == "true" && $equipment_id > 0) {
                $equipment = Equipment::find()->where(['_id' => $equipment_id])->one();
                if ($equipment) {
                    $equipment->deleted = true;
                    $equipment->save();
                }
            }

            // задача
            if ($folder == "false" && $task_template_equipment > 0) {
                self::removeTaskTemplate($task_template_equipment);
            }

            // операция
            if ($folder == "false" && $task_operation_id > 0) {
                self::removeTaskOperation($task_operation_id);
            }
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * Creates a new TaskTemplate and correlation model.
     * @return mixed
     * @var $model TaskTemplate
     */
    public
    function actionNew()
    {
        $equipment_id = 0;
        if (isset($_POST['equipment_id']))
            $equipment_id = $_POST['equipment_id'];

        if (isset($_POST['taskTemplateUuid']))
            $model = TaskTemplate::find()->where(['uuid' => $_POST['taskTemplateUuid']])->one();
        else
            $model = new TaskTemplate();
        $request = Yii::$app->getRequest();
        MainFunctions::log("tree.log", "[new] new taskTemplate");
        if ($request->isPost && $model->load($request->post())) {
            if (isset($_POST["TaskTemplate"]["normative"]))
                $model->normative = $_POST["TaskTemplate"]["normative"];
            if (isset($_POST["TaskTemplate"]["normative"]))
                $model->description = $_POST["TaskTemplate"]["description"];
            if (isset($_POST["TaskTemplate"]["title"]))
                $model->title = $_POST["TaskTemplate"]["title"];
            if (isset($_POST['taskTemplateUuid'])) {
                $model->save();
                return $this->redirect('tree');
            }
            $model->taskTypeUuid = $_POST["TaskTemplate"]["taskTypeUuid"];
            $model->uuid = MainFunctions::GUID();
            $model->save();
            MainFunctions::log("tree.log", "[new] new TaskTemplate " . json_encode($model->errors));
            if ($model->validate() && $equipment_id > 0) {
                $equipment = Equipment::find()->where(['_id' => $equipment_id])->one();
                if ($equipment) {
                    $taskTemplateEquipment = new TaskTemplateEquipment();
                    $taskTemplateEquipment->equipmentUuid = $equipment['uuid'];
                    $taskTemplateEquipment->taskTemplateUuid = $model->uuid;
                    if (isset($_POST["TaskTemplateEquipment"]["period"]))
                        $taskTemplateEquipment->period = $_POST["TaskTemplateEquipment"]["period"];
                    $taskTemplateEquipment->next_dates = "";
                    $taskTemplateEquipment->last_date = date('Y-m-d H:i:s');
                    $taskTemplateEquipment->uuid = MainFunctions::GUID();
                    $taskTemplateEquipment->save();
                } else
                    MainFunctions::log("tree.log", "error create task template");
            }
        }
        return $this->redirect(['tree']);
    }

    /**
     * Creates a new OperationTemplate and correlation model.
     * @return mixed
     * @var $model OperationTemplate
     */
    public
    function actionOperation()
    {
        if (isset($_POST['operationTemplateUuid']))
            $model = OperationTemplate::find()->where(['uuid' => $_POST['operationTemplateUuid']])->one();
        else
            $model = new OperationTemplate();
        $request = Yii::$app->getRequest();
        MainFunctions::log("tree.log", "[new] operationTemplate");
        if ($request->isPost && $model->load($request->post())) {
            if (isset($_POST["OperationTemplate"]["normative"]))
                $model->description = $_POST["OperationTemplate"]["description"];
            $model->title = $_POST["OperationTemplate"]["title"];
            if (isset($_POST['operationTemplateUuid'])) {
                $model->save();
                if (isset($_POST['stageTemplateUuid']))
                    return $this->redirect(['tree', 'modelId' => $_POST["model_id"]]);
                else
                    return $this->redirect('tree');
            }
            $model->uuid = MainFunctions::GUID();
            $model->save();
            MainFunctions::log("tree.log", "[new] new OperationTemplate " . json_encode($model->errors));
            if ($model->validate()) {
                $taskOperation = new TaskOperation();
                $taskOperation->uuid = MainFunctions::GUID();
                $taskOperation->taskTemplateUuid = $_POST["taskTemplateUuid"];
                $taskOperation->operationTemplateUuid = $model["uuid"];
                MainFunctions::log("tree.log", "[new] create new");
                $taskOperation->save();
            }
        } else
            MainFunctions::log("tree.log", "[new] error create operation template: " . json_encode($model->errors));
        return $this->redirect(['tree']);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление существующего шаблона этапа
     *
     * @return mixed
     */
    public function actionChoose()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            $equipment_id = 0;
            $task_id = 0;
            if (isset($_POST["equipment_id"]))
                $equipment_id = $_POST["equipment_id"];
            if (isset($_POST["task_id"]))
                $task_id = $_POST["task_id"];

            // оборудование
            if ($folder == "true" && $equipment_id > 0) {
                $equipment = Equipment::find()->where(['_id' => $equipment_id])->one();
                return $this->renderAjax('_choose_task', [
                    'equipment' => $equipment
                ]);
            }

            // задача
            if ($folder == "false" && $task_id > 0) {
                $taskTemplate = TaskTemplate::find()->where(['_id' => $task_id])->one();
                if ($taskTemplate) {
                    return $this->renderAjax('_choose_operation', [
                        'taskTemplate' => $taskTemplate
                    ]);
                }
            }
        }
        if (isset($_POST["equipment_uuid"]) && isset($_POST["taskTemplateUuid"])) {
            $taskTemplateEquipment = new TaskTemplateEquipment();
            $taskTemplateEquipment->taskTemplateUuid = $_POST["taskTemplateUuid"];
            $taskTemplateEquipment->equipmentUuid = $_POST["equipment_uuid"];
            if (isset($_POST["period"])) {
                $taskTemplateEquipment->period = $_POST["period"];
            }
            $taskTemplateEquipment->next_dates = "";
            $taskTemplateEquipment->last_date = date('Y-m-d H:i:s');
            $taskTemplateEquipment->uuid = MainFunctions::GUID();
            $taskTemplateEquipment->save();
        }

        if (isset($_POST["taskTemplateUuid"])) {
            $taskOperation = new TaskOperation();
            $taskOperation->uuid = MainFunctions::GUID();
            $taskOperation->taskTemplateUuid = $_POST["taskTemplateUuid"];
            $taskOperation->operationTemplateUuid = $_POST["operationTemplateUuid"];
            $taskOperation->save();
        }

        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * функция удаляет, всю цепочку объектов, связанную с taskOperation
     * @param $task_operation_id String Идентификатор этапа операции
     * @return int
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public
    function removeTaskOperation($task_operation_id)
    {
        $taskOperation = TaskOperation::find()->where(['_id' => $task_operation_id])->one();
        if ($taskOperation) {
            $operationTemplatesCount = OperationTemplate::find()->where(['uuid' => $taskOperation['operationUuid']])->count();
            // удаляем только если это единственный шаблон
            if ($operationTemplatesCount == 1) {
                $operationTemplate = OperationTemplate::find()->where(['uuid' => $taskOperation['operationUuid']])->one();
                if ($operationTemplate)
                    $operationTemplate->delete();
            }
            $taskOperation->delete();
            return 0;
        }
    }

    /**
     * функция удаляет, всю цепочку объектов, связанную с taskOperation
     * @param $task_template_equipment String Идентификатор этапа операции
     * @return int
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public
    function removeTaskTemplate($task_template_equipment)
    {
        $taskTemplateEquipment = TaskTemplateEquipment::find()->where(['_id' => $task_template_equipment])->one();
        if ($taskTemplateEquipment) {
            // связан ли шаблон с еще каким-нибудь оборудованием?
            $taskTemplatesCount = TaskTemplateEquipment::find()
                ->where(['taskTemplateUuid' => $taskTemplateEquipment['taskTemplateUuid']])->count();
            // удаляем только если это единственный шаблон
            if ($taskTemplatesCount == 1) {
                $taskTemplate = TaskTemplate::find()->where(['uuid' => $taskTemplateEquipment['taskTemplateUuid']])->one();
                $taskOperationCount = TaskOperation::find()->where(['taskTemplateUuid' => $taskTemplateEquipment['taskTemplateUuid']])->count();
                if ($taskOperationCount == 1) {
                    $taskOperation = TaskOperation::find()->where(['taskTemplateUuid' => $taskTemplateEquipment['taskTemplateUuid']])->one();
                    $operationTemplatesCount = OperationTemplate::find()->where(['uuid' => $taskOperation['operationTemplateUuid']])->count();
                    // удаляем только если это единственный шаблон
                    if ($operationTemplatesCount == 1) {
                        $operationTemplate = OperationTemplate::find()->where(['uuid' => $taskOperation['operationTemplateUuid']])->one();
                        if ($operationTemplate)
                            $operationTemplate->delete();
                    }
                    if ($taskOperation)
                        $taskOperation->delete();
                }
                if ($taskTemplate)
                    $taskTemplate->delete();
                return 1;
            }
        }
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование оборудования, шаблона задачи или операции
     *
     * @return mixed
     */
    public function actionEdit()
    {
        if (isset($_POST["selected_node"])) {
            $operation_id = 0;
            $equipment_id = 0;
            $task_id = 0;
            if (isset($_POST["operation_id"]))
                $operation_id = $_POST["operation_id"];
            if (isset($_POST["equipment_id"]))
                $equipment_id = $_POST["equipment_id"];
            if (isset($_POST["task_id"]))
                $task_id = $_POST["task_id"];
            if (isset($_POST["task_template_equipment"]))
                $task_template_equipment = $_POST["task_template_equipment"];
            // оборудование
            if ($equipment_id > 0) {
                $equipment = Equipment::find()->where(['_id' => $equipment_id])->one();
                return $this->renderAjax('../equipment/_add_form', [
                    'equipment' => $equipment,
                    'reference' => 'task-template/tree'
                ]);
            }

            if ($task_id > 0) {
                $taskTemplate = TaskTemplate::find()->where(['_id' => $task_id])->one();
                $taskTemplateEquipment = TaskTemplateEquipment::find()
                    ->where(['_id' => $task_template_equipment])
                    ->one();
                if ($taskTemplate) {
                    if (isset($_POST["equipment_id"]))
                        $equipment_id = $_POST["equipment_id"];
                    else $equipment_id = 0;
                    return $this->renderAjax('_add_task', [
                        'taskTemplate' => $taskTemplate,
                        'taskTemplateEquipment' => $taskTemplateEquipment,
                        'equipment_id' => $equipment_id
                    ]);
                }
            }

            if ($operation_id > 0) {
                $operationTemplate = OperationTemplate::find()->where(['_id' => $operation_id])->one();
                if ($operationTemplate) {
                    return $this->renderAjax('_add_operation', [
                        'operationTemplate' => $operationTemplate
                    ]);
                }
            }
        }
        $this->enableCsrfValidation = false;
        return 0;
    }
}
