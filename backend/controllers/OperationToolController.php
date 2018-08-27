<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\controllers;

use app\commands\MainFunctions;
use common\components\Errors;
use common\components\FancyTreeHelper;
use common\models\OperationRepairPart;

use common\models\OperationTemplate;
use common\models\OperationType;
use common\models\RepairPart;
use common\models\RepairPartType;
use common\models\Tool;
use common\models\ToolType;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\OperationTool;

use backend\models\OperationSearchTool;

/**
 * OperationToolController implements the CRUD actions for OperationTool model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OperationToolController extends Controller
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
                'class' => VerbFilter::className(),
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
     * Lists all OperationTool models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OperationSearchTool();
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
     * Displays a single OperationTool model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render(
            'view',
            [
                'operationTemplate' => $model->operationTemplate,
                'tool' => $model->tool,
                'model' => $model,
            ]
        );
    }

    /**
     * Creates a new OperationTool model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OperationTool();

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
     * Updates an existing OperationTool model.
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
     * Deletes an existing OperationTool model.
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
     * Finds the OperationTool model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return OperationTool the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OperationTool::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Tree of links between operationTemplate and operation_tool and part
     *
     * @return mixed
     */
    public function actionTree()
    {
        $operationTemplateTree = FancyTreeHelper::buildTree($this,'\common\models\OperationTypeTree',
            '\common\models\OperationType', '\common\models\OperationTemplate', 'operationTypeUuid',true);

        $operationParts = FancyTreeHelper::buildTree($this,'\common\models\RepairPartTypeTree',
            '\common\models\RepairPartType', '\common\models\RepairPart', 'repairPartTypeUuid',false);

        $operationTools = FancyTreeHelper::buildTree($this,'\common\models\ToolTypeTree',
            '\common\models\ToolType', '\common\models\Tool', 'toolTypeUuid',false);

        $partsOperationCount=0;
        $selectParts = array();
        $partOperations = OperationRepairPart::find()
            ->all();
        $selectParts[0]['title'] = 'ЗИП для операции';
        $selectParts[0]['folder'] = true;
        $selectParts[0]['expanded'] = false;
        $selectParts[0]['key'] = 'none';
        foreach ($partOperations as $partOperation) {
            $selectParts[0]['children'][$partsOperationCount]['title'] = $partOperation['operationTemplate']->title;
            $selectParts[0]['children'][$partsOperationCount]['key'] = $partOperation['_id'];
            $partsOperationCount++;
        }

        $toolsOperationCount=0;
        $selectTools = array();
        $toolOperations = OperationTool::find()
            ->all();
        $selectTools[0]['title'] = 'Инструменты для операции';
        $selectTools[0]['folder'] = true;
        $selectTools[0]['expanded'] = false;
        $selectTools[0]['key'] = 'none';
        foreach ($toolOperations as $toolOperation) {
            $selectTools[0]['children'][$toolsOperationCount]['title'] = $toolOperation['operationTemplate']->title;
            $selectTools[0]['children'][$toolsOperationCount]['key'] = $toolOperation['_id'];
            $toolsOperationCount++;
        }

        return $this->render(
            'tree', [
                'operationTemplate' => $operationTemplateTree,
                'operationParts' => $operationParts,
                'selectParts' => $selectParts,
                'operationTools' => $operationTools,
                'selectTools' => $selectTools
            ]
        );
    }

    /**
     * функция отрабатывает сигнал от дерева выбор operationTemplate
     * POST string $uuid - шаблона операции
     * @return mixed
     */

    public function actionCheckParts()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = OperationTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $items = OperationRepairPart::find()->where(['operationTemplateUuid' => $template['uuid']])->all();
                $itemsCount = 0;
                $select[0]['title'] = 'ЗИП для операции '.$template['title'];
                $select[0]['folder'] = true;
                $select[0]['key'] = 'none';
                foreach ($items as $item) {
                    $select[0]['children'][$itemsCount]['_id'] = $item['_id'];
                    $select[0]['children'][$itemsCount]['title'] = $item['repairPart']->title;
                    $select[0]['children'][$itemsCount]['key'] = $item['uuid'];
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
     * функция отрабатывает сигнал от дерева выбор operationTemplate
     * POST string $uuid - шаблона операции
     * @return mixed
     */

    public function actionCheckTools()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = OperationTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $items = OperationTool::find()->where(['operationTemplateUuid' => $template['uuid']])->all();
                $itemsCount = 0;
                $select[0]['title'] = 'Инструменты для операции '.$template['title'];
                $select[0]['folder'] = true;
                $select[0]['key'] = 'none';
                foreach ($items as $item) {
                    $select[0]['children'][$itemsCount]['_id'] = $item['_id'];
                    $select[0]['children'][$itemsCount]['title'] = $item['tool']->title;
                    $select[0]['children'][$itemsCount]['key'] = $item['uuid'];
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
     * функция отрабатывает сигнал от дерева редактирования operationTemplate
     * POST string $uuid - шаблона операции
     * POST string $param - новое название шаблона операции
     * @return mixed
     */
    public function actionEditTemplate()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = OperationTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template['title'] = $_POST["param"];
                //$template['description']=$param;
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
     * функция отрабатывает сигнал от дерева добавления operationTemplate
     * POST string $param - id шаблона операции
     * @return mixed
     */
    public function actionAddTemplate()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["param"])) {
            $operationType = OperationType::find()->where(['_id' => $_POST["param"]])->one();
            if ($operationType) {
                $model = new OperationTemplate();
                $model->uuid = (new MainFunctions)->GUID();
                $model->title = 'Новый шаблон';
                $model->operationTypeUuid = $operationType['uuid'];
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
     * функция отрабатывает сигнал от дерева удаления operationTemplate
     * POST string $uuid - шаблона операции
     * POST string $param - новое название шаблона операции
     * @return mixed
     */
    public function actionDeleteTemplate()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = OperationTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
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
     * функция отрабатывает сигнал перемещения ЗиП в используемые
     * POST string $uuid - шаблона операции
     * POST string $param - новое название шаблона операции
     * @return mixed
     */
    public function actionMovePart()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = OperationTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            $part = RepairPart::find()->where(['_id' => $_POST["param"]])->one();
            if ($template && $part) {
                $model = new OperationRepairPart();
                $model->uuid = (new MainFunctions)->GUID();
                $model->operationTemplateUuid = $template['uuid'];
                $model->repairPartUuid = $part['uuid'];
                if ($model->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            } else return Errors::ERROR_GET_CLASS_ENTITY;
        } else return Errors::WRONG_INPUT_PARAMETERS;
    }

    /**
     * функция отрабатывает сигнал от дерева удаления operationRepairPart
     * POST string $uuid - ЗИП
     * @return mixed
     */
    public function actionDeleteOperationPart()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = OperationRepairPart::find()->where(['_id' => $_POST["uuid"]])->one();
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
     * функция отрабатывает сигнал от дерева редактирования repairPart
     * POST string $uuid - шаблона операции
     * POST string $param - новое название шаблона операции
     * @return mixed
     */
    public function actionEditPart()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = RepairPart::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template['title'] = $_POST["param"];
                //$template['description']=$param;
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
     * функция отрабатывает сигнал от дерева добавления repairPart
     * POST string $param - id типа ЗИП
     * @return mixed
     */
    public function actionAddPart()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["param"])) {
            $repairPartType = RepairPartType::find()->where(['_id' => $_POST["param"]])->one();
            if ($repairPartType) {
                $model = new RepairPart();
                $model->uuid = (new MainFunctions)->GUID();
                $model->title = 'Новый шаблон';
                $model->repairPartTypeUuid = $repairPartType['uuid'];
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
     * функция отрабатывает сигнал от дерева удаления RepairPart
     * POST string $uuid - шаблона операции
     * POST string $param - новое название шаблона операции
     * @return mixed
     */
    public function actionDeletePart()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = RepairPart::find()->where(['_id' => $_POST["uuid"]])->one();
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
     * функция отрабатывает сигнал перемещения инструмента в используемые
     * POST string $uuid - шаблона операции
     * POST string $param -  uuid инструмента
     * @return mixed
     */
    public function actionMoveTool()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = OperationTemplate::find()->where(['_id' => $_POST["uuid"]])->one();
            $tool = Tool::find()->where(['_id' => $_POST["param"]])->one();
            if ($template && $tool) {
                $model = new OperationTool();
                $model->uuid = (new MainFunctions)->GUID();
                $model->operationTemplateUuid = $template['uuid'];
                $model->toolUuid = $tool['uuid'];
                $model->quantity = 0;
                if ($model->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            } else return Errors::ERROR_GET_CLASS_ENTITY;
        } else return Errors::WRONG_INPUT_PARAMETERS;
    }

    /**
     * функция отрабатывает сигнал от дерева удаления operationRepairPart
     * POST string $uuid - инструмента операции
     * @return mixed
     */
    public function actionDeleteOperationTool()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = OperationTool::find()->where(['_id' => $_POST["uuid"]])->one();
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
     * функция отрабатывает сигнал от дерева редактирования tool
     * POST string $uuid - шаблона операции
     * POST string $param - новое название инструмента
     * @return mixed
     */
    public function actionEditTool()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"]) && isset($_POST["param"])) {
            $template = Tool::find()->where(['_id' => $_POST["uuid"]])->one();
            if ($template) {
                $template['title'] = $_POST["param"];
                //$template['description']=$param;
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
     * функция отрабатывает сигнал от дерева добавления инструмента
     * POST string $param - id типа инструмента
     * @return mixed
     */
    public function actionAddTool()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["param"])) {
            $toolType = ToolType::find()->where(['_id' => $_POST["param"]])->one();
            if ($toolType) {
                $model = new Tool();
                $model->uuid = (new MainFunctions)->GUID();
                $model->title = 'Новый шаблон';
                $model->toolTypeUuid = $toolType['uuid'];
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
     * функция отрабатывает сигнал от дерева удаления Tool
     * POST string $uuid - шаблона операции
     * @return mixed
     */
    public function actionDeleteTool()
    {
        $this->enableCsrfValidation = false;
        if (isset($_POST["uuid"])) {
            $template = Tool::find()->where(['_id' => $_POST["uuid"]])->one();
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
