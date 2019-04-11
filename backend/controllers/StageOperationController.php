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
use common\models\OperationTypeTree;
use common\models\StageTypeTree;
use Yii;
use common\models\StageType;
use common\models\TaskOperation;
use common\models\StageTemplate;
use common\models\OperationType;
use common\models\OperationTemplate;
use backend\models\TaskOperationSearch;
use yii\web\NotFoundHttpException;

/**
 * StageOperationController implements the CRUD actions for StageOperation model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class StageOperationController extends ToirusController
{
    protected $modelClass = TaskOperation::class;

    // отключаем проверку для внешних запросов
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id === 'move') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all StageOperation models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskOperationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single StageOperation model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
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
     * функция отрабатывает сигналы от дерева и выполняет следующие действия
     * 1. перенос operationTemplate в stageOperation ($action = 1, $uuid - operationTemplate._id)
     * 2. удаление operationTemplate ($action = 2, $uuid - operationTemplate._id)
     * 3. редактирование operationTemplate ($action = 3, $uuid - operationTemplate._id, $param - operationTemplate::title)
     * 4. добавление operationTemplate ($action = 4, $param - operationTemplate::title)
     * 5. выбор stageTemplate ($action = 5, $uuid - stageTemplate._id)
     * 6. удаление stageTemplate ($action = 6, $uuid - stageTemplate._id)
     * 7. редактирование stageTemplate ($action = 7, $uuid - stageTemplate._id, $param - stageTemplate::title)
     * 8. добавление stageTemplate ($action = 8, $param - stageTemplate::title)
     * 9. удаление stageOperation из списка ($action = 9, $uuid - stageTemplate::id)
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionMove()
    {
        $action=0;
        $id='';
        $param='';
        $folder = false;
        if (isset($_POST["action"]))
            $action = $_POST["action"];
        if (isset($_POST["uuid"]))
            $id = $_POST["uuid"];
        if (isset($_POST["param"]))
            $param = $_POST["param"];
        if (isset($_POST["folder"]))
            $folder = $_POST["folder"];
        $this->enableCsrfValidation = false;

        if ($action==1) {
            if ($param!='' && $id!='') {
                $template = StageTemplate::find()->where(['_id' => $id])->one();
                $operation = OperationTemplate::find()->where(['_id' => $param])->one();
                if ($template && $operation) {
                    $model = new TaskOperation();
                    $model->uuid = (new MainFunctions)->GUID();
                    $model->stageTemplateUuid = $template['uuid'];
                    $model->operationTemplateUuid = $operation['uuid'];
                    if ($model->save())
                        return 1;
                    }
                    else return 2;
            }
        }
        if ($action==2) {
            $template = OperationTemplate::find()->where(['_id' => $id])->one();
            if ($template !== null) {
                if ($template->delete())
                    return 1;
            }
        }
        if ($action==3) {
            $template = OperationTemplate::find()->where(['_id' => $id])->one();
            if ($template) {
                $template['title'] = $param;
                $template['description']=$param;
                if ($template->save())
                    return 1;
            }
        }
        if ($action==4) {
            if ($param!='') {
                $template = OperationType::find()->where(['_id' => $param])->one();
                if ($template) {
                    $model = new OperationTemplate();
                    $model->uuid = (new MainFunctions)->GUID();
                    $model->title = 'Новый шаблон';
                    $model->operationTypeUuid = $template['uuid'];
                    $model->description = 'Новый шаблон';
                    $model->normative = 0;
                    if ($model->save()) {
                        $model->refresh();
                        return $model->_id;
                    }
                }
            }
            return Errors::GENERAL_ERROR;
        }
        if ($action==5) {
            if ($id!='' && !$folder) {
                $template = StageTemplate::find()->where(['_id' => $id])->one();
                if ($template) {
                    $stageOperations = TaskOperation::find()->where(['stageTemplateUuid' => $template['uuid']])->all();
                    $stageOperationCount = 0;
                    $select[0]['title'] = 'Шаблоны операций этапа '. $template['title'];
                    $select[0]['folder'] = true;
                    $select[0]['key'] = 'none';
                    foreach ($stageOperations as $stageOperation) {
                        $select[0]['children'][$stageOperationCount]['_id'] = $stageOperation['_id'];
                        $select[0]['children'][$stageOperationCount]['title'] = $stageOperation['operationTemplate']->title;
                        $select[0]['children'][$stageOperationCount]['key'] = $stageOperation['_id'];
                        $stageOperationCount++;
                    }
                    return json_encode($select);
                }
            }
        }
        if ($action==6) {
            $template = StageTemplate::find()->where(['_id' => $id])->one();
            if ($template) {
                $template->delete();
            }
        }
        if ($action==7) {
            if ($param!='') {
                $stageType = StageType::find()->where(['_id' => $param])->one();
                if ($stageType) {
                    $model = new StageTemplate();
                    $model->uuid = (new MainFunctions)->GUID();
                    $model->title = 'Новый шаблон';
                    $model->stageTypeUuid = $stageType['uuid'];
                    $model->description = 'Новый шаблон';
                    $model->normative = 0;
                    if ($model->save()) {
                        $model->refresh();
                        return $model->_id;
                    }
                    else
                        return Errors::GENERAL_ERROR;
                }
            }
            return Errors::GENERAL_ERROR;
        }
        if ($action==8) {
            $template = StageTemplate::find()->where(['_id' => $id])->one();
            if ($template) {
                $template['title'] = $param;
                $template['description']=$param;
                if($template->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            }
            return Errors::GENERAL_ERROR;
        }
        if ($action==9) {
            $template = TaskOperation::find()->where(['_id' => $id])->one();
            if ($template) {
                if ($template->delete())
                    return Errors::OK;
                return Errors::ERROR_SAVE;
            }
        }
        return Errors::OK;
    }

    /**
     * Tree of stage and operation templates
     *
     * @return mixed
     */
    public function actionTree()
    {
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
            return $this->render('tree', ['stageTemplate' => [], 'operationTemplate' => [], 'select' => []]);
        }

        $types = StageType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $tree[] = [
                'title' => $types[$node_id]->title,
                'key' => $types[$node_id]->_id."",
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

        $indexTable = array();
        $typesTree = OperationTypeTree::find()
            ->from([OperationTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                OperationType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->orderBy('title')
            ->all();

        FancyTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render('tree', ['stageTemplate' => [], 'operationTemplate' => [], 'select' => []]);
        }

        $types = OperationType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $tree[] = [
                'title' => $types[$node_id]->title,
                'folder' => true,
                'expanded' => false,
                'key' => $node_id."",
                'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }

        unset($indexTable);
        unset($types);

        $operationTemplateTree = FancyTreeHelper::resetMulti(
            $tree, OperationType::class, OperationTemplate::class, 'operationTypeUuid'
        );
        unset($tree);

        $stageOperationCount=0;
        $select = array();
        $stageOperations = TaskOperation::find()
            //->asArray()
            ->all();
        $select[0]['title'] = 'Шаблоны операций этапа';
        $select[0]['folder'] = true;
        $select[0]['expanded'] = true;
        $select[0]['key'] = 'none';
        foreach ($stageOperations as $stageOperation) {
            $select[0]['children'][$stageOperationCount]['title'] = $stageOperation['operationTemplate']->title;
            $select[0]['children'][$stageOperationCount]['key'] = $stageOperation['_id'];
            $stageOperationCount++;
        }

        if (!$stageTemplateTree)
            $stageTemplateTree=[];
        return $this->render(
            'tree', [
                'stageTemplate' => $stageTemplateTree,
                'operationTemplate' => $operationTemplateTree,
                'select' => $select
            ]
        );
    }

    /**
     * Creates a new StageOperation model.
     * If creation is successful, the browser will be redirected to
     * the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaskOperation();

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
     * Updates an existing StageOperation model.
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
     * Deletes an existing StageOperation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the StageOperation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return TaskOperation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskOperation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
