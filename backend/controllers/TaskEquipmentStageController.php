<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Views
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\controllers;

use app\commands\MainFunctions;
use common\components\Errors;
use common\components\FancyTreeHelper;
use common\models\Equipment;
use common\models\EquipmentModel;
use common\models\EquipmentStage;
use common\models\EquipmentType;
use common\models\EquipmentTypeTree;
use common\models\StageType;
use common\models\StageTypeTree;
use common\models\TaskTemplate;
use common\models\TaskType;
use common\models\TaskTypeTree;
use Yii;
use common\models\TaskEquipmentStage;
use backend\models\TaskEquipmentStageSearch as TaskEquipmentStageSearch;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskEquipmentStageController implements the CRUD actions for
 * TaskEquipmentStage model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class TaskEquipmentStageController extends Controller
{
    // отключаем проверку для внешних запросов
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
     * Behaviors.
     *
     * @inheritdoc
     *
     * @return array
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

    /**
     * Lists all TaskEquipmentStage models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskEquipmentStageSearch();
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
     * Displays a single TaskEquipmentStage model.
     *
     * @param integer $id Id
     *
     * @return mixed
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
     * Creates a new TaskEquipmentStage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaskEquipmentStage();

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
     * Updates an existing TaskEquipmentStage model.
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
     * Deletes an existing TaskEquipmentStage model.
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
     * Finds the TaskEquipmentStage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return TaskEquipmentStage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskEquipmentStage::findOne($id)) !== null) {
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
                $tree['children'][] = ['title' => $model['title'], 'key' => $model['_id']."", 'folder' => true];
                $childIdx = count($tree['children'])-1;
                $equipments = $entityClass::find()->where(['equipmentModelUuid' => $model['uuid']])->all();
                foreach ($equipments as $equipment) {
                    $tree['children'][$childIdx]['children'][] =
                        ['title' => $equipment['title'], 'key' => $equipment['_id'], 'folder' => true];
                    $childIdx2 = count($tree['children'][$childIdx]['children'])-1;
                    $equipmentStages = $entityInClass::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                    foreach ($equipmentStages as $equipmentStage) {
                        $tree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                            ['title' => $equipmentStage['stageOperation']['stageTemplate']['title'].' :: '.
                                $equipmentStage['stageOperation']['operationTemplate']['title'],
                                'key' => $equipmentStage['_id']];
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
        $typesTree = TaskTypeTree::find()
            ->from([TaskTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                TaskType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->orderBy('title')
            ->all();

        FancyTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render('tree', ['templates' => []]);
        }

        $types = TaskType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $tree[] = [
                'title' => $types[$node_id]->title,
                'key' => $node_id,
                'folder' => true,
                'expanded' => true,
                'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }
        unset($indexTable);
        unset($types);

        $taskTree = FancyTreeHelper::resetMulti(
            $tree, TaskType::class, TaskTemplate::class, 'taskTypeUuid'
        );
        unset($tree);

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
            return $this->render('tree', ['taskTree' => [], 'equipmentTree' => [], 'taskEquipmentStageTree' => []]);
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
        $equipmentTree = self::addEquipmentToTree ($tree,
            EquipmentModel::class,
            Equipment::class,
            EquipmentStage::class,
            'equipmentModelUuid'
        );

        $indexTable = array();
        $typesTree = StageTypeTree::find()
            ->from([StageTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                StageType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->orderBy('title')
            ->all();

        FancyTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render('tree', ['taskTree' => [], 'equipmentTree' => [], 'taskEquipmentStageTree' => []]);
        }

        $taskEquipmentStageCount=0;
        $taskEquipmentStageTree = array();
        $taskEquipmentStages = TaskEquipmentStage::find()
            ->all();
        $taskEquipmentStageTree[0]['title'] = 'Этапы задач для оборудования';
        $taskEquipmentStageTree[0]['folder'] = true;
        $taskEquipmentStageTree[0]['expanded'] = true;
        $taskEquipmentStageTree[0]['key'] = 'none';
        foreach ($taskEquipmentStages as $taskEquipmentStage) {
            $taskEquipmentStageTree[0]['children'][$taskEquipmentStageCount]['title'] =
                $taskEquipmentStage['taskTemplate']->title.' - '.$taskEquipmentStage['equipmentStage']['equipment']->title;
            $taskEquipmentStageTree[0]['children'][$taskEquipmentStageCount]['key'] = $taskEquipmentStage['_id'];
            $taskEquipmentStageCount++;
        }

        return $this->render(
            'tree', [
                'taskTree' => $taskTree,
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
        if (isset($_POST["uuid"])) {
            $taskTemplate = TaskTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($taskTemplate) {
                $items = TaskEquipmentStage::find()->where(['taskTemplateUuid' => $taskTemplate['uuid']])->all();
                $itemsCount = 0;
                $select[0]['title'] = 'Шаблоны для задачи '. $taskTemplate['title'];
                $select[0]['folder'] = true;
                $select[0]['key'] = 'none';
                foreach ($items as $item) {
                    $select[0]['children'][$itemsCount]['_id'] = $item['_id'];
                    $select[0]['children'][$itemsCount]['title'] =
                        $item['equipmentStage']['equipment']->title.' - '.
                        $item['equipmentStage']['stageOperation']['operationTemplate']->title;
                    $select[0]['children'][$itemsCount]['key'] = $item['_id'];
                    $itemsCount++;
                }
                return json_encode($select);
            }
        }
        else
            return Errors::WRONG_INPUT_PARAMETERS;
        return Errors::GENERAL_ERROR;
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
                if($template->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            }
        }
        else
            return Errors::WRONG_INPUT_PARAMETERS;
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
        if (isset($_POST["uuid"])) {
            $template = TaskTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            }
            else
                return Errors::ERROR_SAVE;
        }
        else return Errors::WRONG_INPUT_PARAMETERS;
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
        }
        else return Errors::GENERAL_ERROR;
        return Errors::GENERAL_ERROR;
    }

    /**
     * функция отрабатывает сигнал перемещения шаблона в используемые
     * POST string $uuid - шаблона
     * POST string $param - uuid оборудования
     * @return mixed
     */
    public function actionMoveStage()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $equipmentStage = EquipmentStage::find()->where(['_id' => $_POST["param"]])->one();
            $template = TaskTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($equipmentStage && $template) {
                $model = new TaskEquipmentStage();
                $model->uuid = (new MainFunctions)->GUID();
                $model->taskTemplateUuid = $template['uuid'];
                $model->equipmentStageUuid = $equipmentStage['uuid'];
                $model->period = '0';
                if ($model->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            } else return Errors::ERROR_GET_CLASS_ENTITY;
        } else return Errors::WRONG_INPUT_PARAMETERS;
    }

    /**
     * функция отрабатывает сигнал от дерева удаления EquipmentStage
     * POST string $uuid
     * @return mixed
     */
    public function actionDeleteStage()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = TaskEquipmentStage::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template->delete();
                return Errors::OK;
            }
            else
                return Errors::ERROR_SAVE;
        }
        else return Errors::WRONG_INPUT_PARAMETERS;
    }
}
