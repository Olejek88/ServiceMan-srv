<?php

namespace backend\controllers;

use common\components\TypeTreeHelper;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\TaskTypeTree;
use Yii;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use common\models\TaskTemplate;
use common\models\TaskType;
use backend\models\TaskSearchTemplate;

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
        if (is_array($tree)) {
            $tree = array_slice($tree, 0);
            foreach ($tree AS $key => $value) {
                if (is_array($value)) {
                    $tree[$key] = self::addEquipmentStageToTree(
                        $value, $modelClass, $entityClass, $linkField
                    );
                }
            }
        }

        if (isset($tree['key'])) {
            $type = EquipmentType::findOne($tree['key']);
            $models = $modelClass::find()->where(['equipmentTypeUuid' => $type['uuid']])->all();
            foreach ($models as $model) {
                $expanded = false;
                if (isset($_GET['modelId']) && $_GET['modelId'] == $model['_id'])
                    $expanded = true;
                $tree['children'][] = ['title' => $model['title'], 'key' => $model['_id'] . "",
                    'expanded' => $expanded, 'folder' => true, 'model' => true, 'model_id' => $model['_id'] . "",
                    'operation' => false];
                $childIdx = count($tree['children']) - 1;

                $equipmentStages = EquipmentStage::find()
                    ->select('equipment_stage.*')
                    ->where(['equipmentModelUuid' => $model['uuid']])
                    ->joinWith(['stageOperation so'])
                    ->groupBy(['stageTemplateUuid'])
                    ->all();
                $equipmentStageCount = 0;
                foreach ($equipmentStages as $equipmentStage) {
                    $taskEquipmentStage = TaskEquipmentStage::find()
                        ->where(['equipmentStageUuid' => $equipmentStage['uuid']])
                        ->one();
                    $period = "";
                    if ($taskEquipmentStage) {
                        $period_text = $taskEquipmentStage["period"];
                        if ($taskEquipmentStage["period"] == "@hourly")
                            $period_text = "каждый час";
                        if ($taskEquipmentStage["period"] == "@daily")
                            $period_text = "каждый день";
                        if ($taskEquipmentStage["period"] == "@yearly")
                            $period_text = "раз в год";
                        if ($taskEquipmentStage["period"] == "@monthly")
                            $period_text = "каждый месяц";
                        $period = '<div class="progress"><div class="critical4">' .
                            $period_text . '</div></div>';
                        if ($taskEquipmentStage["period"] == "") {
                            $period = '<div class="progress"><div class="critical5">не задан</div></div>';
                        }
                        $period = Html::a($period,
                            ['/task-equipment-stage/period', 'taskEquipmentStageUuid' => $taskEquipmentStage['uuid']],
                            [
                                'title' => 'Задать период',
                                'data-toggle' => 'modal',
                                'data-target' => '#modalStatus',
                            ]
                        );
                    }

                    $type = '<div class="progress"><div class="critical5">' .
                        $equipmentStage["stageOperation"]["stageTemplate"]["stageType"]["title"] . '</div></div>';
                    $tree['children'][$childIdx]['children'][] =
                        ['key' => $equipmentStage["stageOperation"]["stageTemplate"]["_id"] . "",
                            'folder' => false,
                            'model_id' => $model['_id'],
                            'model' => false,
                            'uuid' => $equipmentStage["stageOperation"]["stageTemplate"]["uuid"],
                            'operation' => false,
                            'created' => $equipmentStage["stageOperation"]["stageTemplate"]["changedAt"],
                            'description' => $equipmentStage["stageOperation"]["stageTemplate"]["description"],
                            'types' => $type,
                            'expanded' => true,
                            'period' => $period,
                            'normative' => $equipmentStage["stageOperation"]["stageTemplate"]["normative"],
                            'title' => mb_convert_encoding($equipmentStage["stageOperation"]["stageTemplate"]["title"], 'UTF-8', 'UTF-8'),
                        ];

                    $stageOperations = StageOperation::find()
                        ->where(['stageTemplateUuid' => $equipmentStage["stageOperation"]["stageTemplate"]["uuid"]])
                        ->all();
                    $stageOperationCount = 0;
                    foreach ($stageOperations as $stageOperation) {
                        if ($stageOperation["operationTemplate"]["uuid"] != OperationTemplate::DEFAULT_OPERATION) {
                            $type = '<div class="progress"><div class="critical5">' .
                                $stageOperation["operationTemplate"]["operationType"]["title"] . '</div></div>';
                            $tree['children'][$childIdx]['children'][$equipmentStageCount]['children'][$stageOperationCount] =
                                ['key' => $stageOperation["operationTemplate"]["_id"] . "",
                                    'folder' => false,
                                    'expanded' => true,
                                    'model_id' => $model['_id'],
                                    'created' => $stageOperation["operationTemplate"]["changedAt"],
                                    'types' => $type,
                                    'uuid' => $stageOperation["operationTemplate"]["uuid"],
                                    'normative' => $stageOperation["operationTemplate"]["normative"],
                                    'description' => mb_convert_encoding(substr($stageOperation["operationTemplate"]["description"],0,150), 'UTF-8', 'UTF-8'),
                                    'model' => false,
                                    'operation' => true,
                                    'title' => mb_convert_encoding($stageOperation["operationTemplate"]["title"], 'UTF-8', 'UTF-8'),
                                ];
                            $stageOperationCount++;
                        }
                    }
                    $equipmentStageCount++;
                }
            }
        }
        return ($tree);
    }


}
