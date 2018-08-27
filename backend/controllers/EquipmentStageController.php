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
use common\models\EquipmentType;
use common\models\EquipmentTypeTree;
use common\models\OperationTemplate;
use common\models\StageOperation;
use common\models\StageTemplate;
use common\models\StageType;
use common\models\StageTypeTree;
use Yii;
use common\models\EquipmentStage;
use backend\models\EquipmentStageSearch;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EquipmentStageController implements the CRUD actions for EquipmentStage model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class EquipmentStageController extends Controller
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
     * Lists all EquipmentStage models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipmentStageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single EquipmentStage model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render(
            'view', [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new EquipmentStage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipmentStage();

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
     * Updates an existing EquipmentStage model.
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
     * Deletes an existing EquipmentStage model.
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
     * Finds the EquipmentStage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return EquipmentStage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentStage::findOne($id)) !== null) {
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
     * @param string $linkField Поле через которое связывается
     *
     * @return mixed
     */
    public function addEquipmentToTree($tree, $modelClass, $entityClass, $linkField)
    {
        if (is_array($tree)) {
            $tree = array_slice($tree, 0);
            foreach ($tree AS $key => $value) {
                if (is_array($value)) {
                    $tree[$key] = self::addEquipmentToTree(
                        $value, $modelClass, $entityClass, $linkField
                    );
                }
            }
        }

        if (isset($tree['key'])) {
            $type = EquipmentType::findOne($tree['key']);
            $models = $modelClass::find()->where(['equipmentTypeUuid' => $type['uuid']])->all();
            foreach ($models as $model) {
                $expanded=false;
                if (isset($_GET['typeUuid']) && $_GET['typeUuid']==$type->uuid)
                    $expanded=true;
                $tree['children'][] = ['title' => $model['title'], 'key' => $model['_id']."",
                    'expanded' => $expanded, 'folder' => true];
                $childIdx = count($tree['children'])-1;
                $equipments = $entityClass::find()->where(['equipmentModelUuid' => $model['uuid']])->all();
                foreach ($equipments as $equipment) {
                    //if (isset($_GET['uuid']) && $_GET['uuid']==$equipment['uuid'])
                    $tree['children'][$childIdx]['children'][] =
                            ['title' => $equipment['title'], 'key' => $equipment['_id'].""];
                }
            }
        }
        return ($tree);
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
    public function addOperationTemplateToTree($tree, $modelClass, $entityClass, $entityInClass, $linkField)
    {
        if (is_array($tree)) {
            $tree = array_slice($tree, 0);
            foreach ($tree AS $key => $value) {
                if (is_array($value)) {
                    $tree[$key] = self::addOperationTemplateToTree(
                        $value, $modelClass, $entityClass, $entityInClass, $linkField
                    );
                }
            }
        }

        if (isset($tree['key'])) {
            $type = StageType::findOne($tree['key']);
            $stageTemplates = StageTemplate::find()->where(['stageTypeUuid' => $type['uuid']])->all();
            foreach ($stageTemplates as $stageTemplate) {
                $tree['children'][] = ['title' => $stageTemplate['title'], 'key' => $stageTemplate['_id']."", 'folder' => true];
                $childIdx = count($tree['children'])-1;
                $equipments = $entityClass::find()->where(['equipmentModelUuid' => $stageTemplate['uuid']])->all();
                foreach ($equipments as $equipment) {
                    $tree['children'][$childIdx]['children'][] =
                        ['title' => $equipment['title'], 'key' => $equipment['_id']."", 'folder' => true];
                    $childIdx2 = count($tree['children'][$childIdx]['children'])-1;
                    $equipmentStages = $entityInClass::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                    foreach ($equipmentStages as $equipmentStage) {
                        $tree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                            ['title' => '<a href="/equipment-stage/tree">'.
                                $equipmentStage['stageOperation']['stageTemplate']['title'].'</a>',
                                'key' => $equipmentStage['_id'] . ""];
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
            return $this->render('tree', ['stageTemplate' => [], 'equipment' => [], 'equipmentStage' => []]);
        }

        $types = EquipmentType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $expanded=false;
            if (isset($_GET['typeUuid']) && $_GET['typeUuid']==$types[$node_id]->uuid)
                $expanded=true;
            $tree[] = [
                'title' => $types[$node_id]->title,
                'key' => $node_id,
                'folder' => true,
                'expanded' => $expanded,
                'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }
        unset($indexTable);
        unset($types);
        $equipmentTree = self::addEquipmentToTree ($tree,
            EquipmentModel::class,
            Equipment::class,
            'equipmentModelUuid'
        );
        //var_dump($equipment);

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
            return $this->render('tree', ['stageTemplate' => [], 'equipment' => [], 'equipmentStage' => []]);
        }

        $types = StageType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $tree[] = [
                'title' => '<a href="/stage-operation/tree">'.$types[$node_id]->title.'</a>',
                'key' => $node_id."",
                'folder' => true,
                'expanded' => true,
                'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }

        unset($indexTable);
        unset($types);

        $stageTemplateTree = FancyTreeHelper::resetMulti(
            $tree, StageType::class, StageTemplate::class, 'stageTypeUuid'
        );
        unset($tree);

        $equipmentStageCount=0;
        $equipmentStageTree = array();
        $equipmentStages = EquipmentStage::find()
            ->all();
        $equipmentStageTree[0]['title'] = 'Этапы задач для оборудования';
        $equipmentStageTree[0]['folder'] = true;
        $equipmentStageTree[0]['expanded'] = true;
        $equipmentStageTree[0]['key'] = 'none';
        foreach ($equipmentStages as $equipmentStage) {
            $equipmentStageTree[0]['children'][$equipmentStageCount]['title'] =
                $equipmentStage['stageOperation']['stageTemplate']->title.' :: '.
                $equipmentStage['stageOperation']['operationTemplate']->title;

            $equipmentStageTree[0]['children'][$equipmentStageCount]['key'] = $equipmentStage['_id'];
            $equipmentStageCount++;
        }

        return $this->render(
            'tree', [
                'stageTemplate' => $stageTemplateTree,
                'equipment' => $equipmentTree,
                'equipmentStage' => $equipmentStageTree
            ]
        );
    }

    /**
     * функция отрабатывает сигнал от дерева выбор equipment
     * POST string $uuid - equipment
     * @return mixed
     */

    public function actionCheckEquipment()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $equipment = Equipment::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($equipment) {
                $items = EquipmentStage::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                $itemsCount = 0;
                $select[0]['title'] = 'Шаблоны для оборудования '.$equipment['title'];
                $select[0]['folder'] = true;
                $select[0]['key'] = 'none';
                foreach ($items as $item) {
                    $select[0]['children'][$itemsCount]['_id'] = $item['_id'];
                    $select[0]['children'][$itemsCount]['title'] =
                        $item['stageOperation']['stageTemplate']->title.' :: '.
                        $item['stageOperation']['operationTemplate']->title;
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
     * функция отрабатывает сигнал от дерева редактирования Equipment
     * POST string $uuid - оборудования
     * POST string $param - новое название
     * @return mixed
     */
    public function actionEditEquipment()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = Equipment::find()->where(['_id' => $_POST["uuid"]])->one();
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
     * функция отрабатывает сигнал перемещения шаблона в используемые
     * POST string $uuid - шаблона
     * POST string $param - uuid оборудования
     * @return mixed
     */
    public function actionMoveStage()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = StageTemplate::find()->where(['_id' => $_POST["param"]])->one();
            $equipment = Equipment::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template && $equipment) {
                // по-умолчанию выбираем ВСЕ связи для данного шаблона этапа
                // пользователю проще будет удалить лишние шаблоны-операций через связь StageOperation
                $stageOperations = StageOperation::find()->where(['stageTemplateUuid' => $template['uuid']])->all();
                foreach ($stageOperations as $stageOperation) {
                    $model = new EquipmentStage();
                    $model->uuid = (new MainFunctions)->GUID();
                    $model->stageOperationUuid = $stageOperation['uuid'];
                    $model->equipmentUuid = $equipment['uuid'];
                    if (!$model->save())
                        return Errors::ERROR_SAVE;
                }
                return Errors::OK;
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
            $template = EquipmentStage::find()->where(['_id' => $_POST["uuid"]])->one();
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
