<?php

namespace backend\controllers;

use backend\models\TaskSearchTemplate;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\TaskOperation;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipment;
use common\models\TaskTypeTree;
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
        /*        $indexTable = array();
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
                    return $this->render('tree', ['defects' => [], 'equipment' => [],
                        'registers' => [], 'operations' => [], $defectsCount = 0]);
                }

                $indexTable = array();
                $types = EquipmentType::find()->indexBy('_id')->all();
                $tree = array();
                $startLevel = 1;
                foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
                    $expanded = false;
                    if (isset($_GET['typeUuid']) && $_GET['typeUuid'] == $types[$node_id]->uuid)
                        $expanded = true;
                    $tree[] = [
                        'title' => $types[$node_id]->title,
                        'key' => $node_id,
                        'folder' => true,
                        'model' => false,
                        'model_id' => 0,
                        'expanded' => $expanded,
                        'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
                    ];
                }
                unset($indexTable);
                unset($types);*/

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
            $expanded = false;
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
                                'title' => mb_convert_encoding($taskOperation["operationTemplate"]["title"], 'UTF-8', 'UTF-8'),
                            ];
                    }
                }
            }
        }
        return ($tree);
    }


}
