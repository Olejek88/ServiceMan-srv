<?php

namespace backend\controllers;

use common\components\MainFunctions;
use common\models\Defect;
use common\models\Equipment;
use common\models\OperationFile;
use common\models\OperationTool;
use common\models\StageTemplate;
use common\models\TaskTemplate;
use common\models\TaskType;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

use common\models\Users;
use common\models\Orders;
use common\models\Task;
use common\models\Operation;
use common\models\OrderLevel;
use common\models\TaskStatus;
use common\models\TaskVerdict;
use common\models\OrderStatus;
use common\models\OrderVerdict;
use common\models\Stage;
use common\models\StageStatus;
use common\models\StageVerdict;
use common\models\OperationStatus;
use common\models\OperationVerdict;
use common\models\OperationTemplate;

use backend\models\OrderSearch;
use yii2fullcalendar\models\Event;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
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
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Orders models.
     * @return mixed
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Orders::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'startDate') {
                $model['startDate'] = date("Y-m-d H:i:s", $_POST['Orders'][$_POST['editableIndex']]['startDate']);
            }
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Orders'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'reason') {
                $model['reason'] = $_POST['Orders'][$_POST['editableIndex']]['reason'];
            }
            if ($_POST['editableAttribute'] == 'userUuid') {
                $model['userUuid'] = $_POST['Orders'][$_POST['editableIndex']]['userUuid'];
            }
            if ($_POST['editableAttribute'] == 'authorUuid') {
                $model['authorUuid'] = $_POST['Orders'][$_POST['editableIndex']]['authorUuid'];
            }
            if ($_POST['editableAttribute'] == 'orderStatusUuid') {
                $model['orderStatusUuid'] = $_POST['Orders'][$_POST['editableIndex']]['orderStatusUuid'];
            }
            if ($_POST['editableAttribute'] == 'orderVerdictUuid') {
                $model['orderVerdictUuid'] = $_POST['Orders'][$_POST['editableIndex']]['orderVerdictUuid'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Orders models.
     * @return mixed
     */
    public function actionExcel()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->renderPartial('excel', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a timeline of Order
     * @return mixed
     */
    public function actionTimeline()
    {
        if (isset($_GET['id'])) {
            $order = Orders::find()
                ->where(['_id' => $_GET['id']])
                ->one();
        }
        else {
            $order = Orders::find()->one();
        }

        $tree=[];
        $taskCount=0;
        $tasks = Task::find()
            ->where(['orderUuid' => $order['uuid']])
            ->all();
        foreach ($tasks as $task) {
            if ($task['startDate']>0) $startDate = date("M j, Y", strtotime($task['startDate']));
                else $startDate = 'не начиналась';
            if ($task['endDate']>0) $endDate = date("M j, Y", strtotime($task['endDate']));
                else $endDate = 'не закончилась';
            $tree[] = ['title' => $task['taskTemplate']->title, 'comment' => $task['comment'],
                'type' => $task['taskTemplate']['taskType']->title, 'description' => $task['taskTemplate']['description'],
                'user' => $task['order']['user']->name, 'image' => $task['taskTemplate']->image,
                'startDate' => $startDate, 'endDate' => $endDate, 'id' => $task['_id'],
                'status' => $task['taskStatusUuid'], 'verdict' => $task['taskVerdictUuid']];
            $stageCount=0;
            $stages = Stage::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();
            foreach ($stages as $stage) {
                $operationsCount=0;
                if ($stage['startDate']>0) $startDate = date("M j, Y", strtotime($stage['startDate']));
                else $startDate = 'не начиналась';
                if ($stage['endDate']>0) $endDate = date("M j, Y", strtotime($stage['endDate']));
                else $endDate = 'не закончилась';
                $tree[$taskCount]['child'][] = ['title' => $stage['stageTemplate']->title, 'comment' => $stage['comment'],
                    'type' => $stage['stageTemplate']['stageType']->title, 'description' => $stage['stageTemplate']['description'],
                    'startDate' => $startDate, 'endDate' => $endDate, 'id' => $stage['_id'],
                    'equipment' => $stage['equipment']->title.' ['.$stage['equipment']['equipmentModel']->title.'] [SN: '.$stage['equipment']->serialNumber.']',
                    'location' => $stage['equipment']['location']->title, 'image' => $stage['stageTemplate']->image,
                    'status' => $stage['stageStatusUuid'], 'verdict' => $stage['stageVerdictUuid']];
                //$tree[$taskCount][$stageCount][] = ['title' => $stage['stageTemplate']->title.' ['.$stage['comment'].']'];
                $operations = Operation::find()
                    ->where(['stageUuid' => $stage['uuid']])
                    ->all();
                foreach ($operations as $operation) {
                    if ($operation['startDate']>0) $startDate = date("M j, Y", strtotime($operation['startDate']));
                    else $startDate = 'не начиналась';
                    if ($operation['endDate']>0) $endDate = date("M j, Y", strtotime($operation['endDate']));
                    else $endDate = 'не закончилась';
                    $operationFiles = OperationFile::find()
                        ->where(['operationUuid' => $operation['uuid']])
                        ->all();
                    $tree[$taskCount]['child'][$stageCount]['child'][] =
                        ['title' => $operation['operationTemplate']->title, 'comment' => $operation['comment'],
                        'type' => $operation['operationTemplate']['operationType']->title, 'description' => $operation['operationTemplate']['description'],
                        'startDate' => $startDate, 'endDate' => $endDate, 'id' => $operation['_id'],
                            'files' => $operationFiles,
                        'status' => $operation['operationStatusUuid'], 'verdict' => $operation['operationVerdictUuid']];
                    $operationsCount++;
                }
                $stageCount++;
            }
            $taskCount++;
        }
        return $this->render('timeline', [
            'order' => $order,
            'tree' => $tree
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $order = Orders::find()
            ->select('authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid')
            ->where(['_id' => $id])
            ->asArray()
            ->one();
        $author = Users::find()
            ->select('name')
            ->where(['uuid' => $order['authorUuid']])
            ->asArray()
            ->one();
        $user = Users::find()
            ->select('name')
            ->where(['uuid' => $order['userUuid']])
            ->asArray()
            ->one();
        $status = OrderStatus::find()
            ->select('title')
            ->where(['uuid' => $order['orderStatusUuid']])
            ->asArray()
            ->one();
        $verdict = OrderVerdict::find()
            ->select('title')
            ->where(['uuid' => $order['orderVerdictUuid']])
            ->asArray()
            ->one();
        $level = OrderLevel::find()
            ->select('title')
            ->where(['uuid' => $order['orderLevelUuid']])
            ->asArray()
            ->one();
        // return var_dump($level);

        return $this->render('view', [
            'author' => $author,
            'user' => $user,
            'status' => $status,
            'verdict' => $verdict,
            'level' => $level,
            'model' => $this->findModel($id),
        ]);
    }

    public function actionInfo($id)
    {
        /**
         * [Базовые определения]
         * @var [type]
         */
        $model = $this->findModel($id);

        /**
         * [Определения с фильтрами]
         * @var [type]
         */
        $order = Orders::find()
            ->select('uuid, authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid')
            ->where(['_id' => $id])
            ->asArray()
            ->one();
        /**
         * [Выборка автора и исполнителя]
         */
        $author = Users::find()->select('_id, name, whoIs')
            ->where(['uuid' => $model->authorUuid])
            ->one();

        $operator = Users::find()->select('_id, name, whoIs')
            ->where(['uuid' => $model->userUuid])
            ->one();

        $statusTitle = OrderStatus::find()
            ->select('title')
            ->where(['uuid' => $order['orderStatusUuid']])
            ->asArray()
            ->one();

        $verdictTitle = OrderVerdict::find()
            ->select('title')
            ->where(['uuid' => $order['orderVerdictUuid']])
            ->asArray()
            ->one();

        $levelTitle = OrderLevel::find()
            ->select('title')
            ->where(['uuid' => $order['orderLevelUuid']])
            ->asArray()
            ->one();


        /**
         * Выборка задач, этапов и операций для определенного наряда
         */
        $stageIndex = [];
        $stages = [];
        $operations = [];

        $tasks = Task::find()
            ->where(['orderUuid' => $order['uuid']])
            ->asArray()
            ->all();

        $taskStatus = TaskStatus::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $taskVerdict = TaskVerdict::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

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

        foreach ($tasks as $key => $task) {
            $stageIndex[] = Stage::find()
                ->where(['taskUuid' => $task['uuid']])
                ->asArray()
                ->all();

            foreach ($taskStatus as $status) {
                if ($task['taskStatusUuid'] === $status['uuid']) {
                    $tasks[$key]['taskStatusUuid'] = $status['title'];
                }
            }

            foreach ($taskVerdict as $verdict) {
                if ($task['taskVerdictUuid'] === $verdict['uuid']) {
                    $tasks[$key]['taskVerdictUuid'] = $verdict['title'];
                }
            }
        }

        foreach ($stageIndex as $index => $value) {
            foreach ($value as $key => $val) {
                $stages[] = Operation::find()
                    ->where(['stageUuid' => $val['uuid']])
                    ->asArray()
                    ->all();


                foreach ($stageStatus as $status) {
                    if ($val['stageStatusUuid'] === $status['uuid']) {
                        $stageIndex[$index][$key]['stageStatusUuid'] = $status['title'];
                    }
                }

                foreach ($stageVerdict as $verdict) {
                    if ($val['stageVerdictUuid'] === $verdict['uuid']) {
                        $stageIndex[$index][$key]['stageVerdictUuid'] = $verdict['title'];
                    }
                }
            }

            foreach ($stages as $value2) {
                $operations[] = $value2;
            }
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

        // return var_dump($operations);

        return $this->render(
            'info',
            [
                'model' => $model,
                'author' => $author,
                'user' => $operator,
                'status' => $statusTitle,
                'verdict' => $verdictTitle,
                'level' => $levelTitle,
                'tasks' => $tasks,
                'stageIndex' => $stages,
                'stages' => $stageIndex,
                'operationIndex' => $operationIndex,
                'operations' => $operations,
            ]
        );
    }

    public function actionOrder($id)
    {
        /**
         * [Базовые определения]
         * @var [type]
         */
        $model = $this->findModel($id);

        // Открываем файл
        $xls = \PHPExcel_IOFactory::load('files/task.xlsx');
        // Создаем объект класса PHPExcel
        //$xls = new \PHPExcel();
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();
        // Подписываем лист
        $sheet->setTitle('Наряд');

        /**
         * [Определения с фильтрами]
         * @var [type]
         */
        $order = Orders::find()
            ->select('uuid, reason, title, authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid, startDate, closeDate, createdAt')
            ->where(['_id' => $id])
            ->one();
        //$orderFileName = "orders/order_" . $id . ".xlsx";
        /**
         * [Выборка автора и исполнителя]
         */
        $author = Users::find()->select('_id, name, whoIs, contact')
            ->where(['uuid' => $model->authorUuid])
            ->one();

        $operator = Users::find()->select('_id, name, whoIs, contact')
            ->where(['uuid' => $model->userUuid])
            ->one();


        /**
         * Выборка задач, этапов и операций для определенного наряда
         */
        $stageIndex = [];
        $stages = [];
        $equipments = [];
        $equipments_title = [];
        $equipments_cnt = 0;
        $equipment_list = '';

        $tasks = Task::find()
            ->where(['orderUuid' => $order['uuid']])
            //->asArray()
            ->all();

        $nRow = 16;
        $nTask = 1;
        $nStage = 1;
        //$partsCount=0;
        $toolsCount = 0;
        foreach ($tasks as $key => $task) {
            $stageIndex = Stage::find()
                ->where(['taskUuid' => $task['uuid']])
                //->asArray()
                ->all();
            $sheet->setCellValue("C" . $nRow, $nTask . "." . $task['taskTemplate']->title);

//            $est = 0;
//            for ($i = 0; $i < $equipments_cnt; $i++) {
//                if ($equipments[$i] == $task['equipmentUuid']) {
//                    $est = 1;
//                }
//            }
//            if (!$est) {
//                $equipments[$equipments_cnt] = $task['equipmentUuid'];
//                $equipments_title[$equipments_cnt] = $task['equipment']->title;
//                $equipments_cnt++;
//
//                $equipment_list .= $task['equipment']->title . ', ';
//                $sheet->setCellValue("C" . $nRow, $nTask . "." .
//                    $task['taskTemplate']->title . " (" . $task['equipment']->title . ")");
//            }
            $nRow++;
            $nTask++;

            foreach ($stageIndex as $stage) {
                $sheet->setCellValue(
                    "D" . $nRow, $nStage . "." . $stage['stageTemplate']->title
                );
                $est = 0;
                for ($i = 0; $i < $equipments_cnt; $i++) {
                    if ($equipments[$i] == $stage['equipmentUuid']) {
                        $est = 1;
                        $sheet->setCellValue(
                            "D" . $nRow, $nStage . "." . $stage['stageTemplate']->title .
                            " (" . $equipments_title[$i] . ")"
                        );
                    }
                }
                if (!$est) {
                    $equipments[$equipments_cnt] = $stage['equipmentUuid'];
                    $equipments_title[$equipments_cnt] = $stage['equipment']->title;
                    $equipments_cnt++;
                    $equipment_list .= $task['equipment']->title . ', ';
                    $sheet->setCellValue("D" . $nRow, $nStage . "." .
                        $stage['stageTemplate']->title . " (" . $stage['equipment']->title . ")");
                }

                $operations = Operation::find()
                    ->where(['stageUuid' => $stage['uuid']])
                    ->all();

                foreach ($operations as $operation) {
                    $orderTools = OperationTool::find()
                        ->where(['operationTemplateUuid' => $operation['operationTemplate']->uuid])
                        ->all();
                    foreach ($orderTools as $orderTool) {
                        $sheet->setCellValue("C" . (36 + $toolsCount), $orderTool['tool']->title);
                        $toolsCount++;
                    }
                }
                $nRow++;
                $nStage++;
            }
            $nStage = 1;
        }


        if (strlen($order['title']) > 0) {
            $sheet->setCellValue("I1", $id);
            $sheet->setCellValue("E3", $order['title']);
            $sheet->setCellValue("E4", $order['reason']);
            $sheet->setCellValue("E5", $order['startDate']);
            if ($order['closeDate'] != "0000-00-00 00:00:00")
                $sheet->setCellValue("J5", $order['closeDate']);
            $sheet->setCellValue("F14", $order['createdAt']);
        }
        if (strlen($equipment_list) > 0)
            $sheet->setCellValue("C8", $equipment_list);

        $sheet->setCellValue("E11", $author['name']);
        $sheet->setCellValue("J11", $author['whoIs']);
        $sheet->setCellValue("E13", $operator['name']);
        $sheet->setCellValue("J13", $operator['whoIs']);
        $sheet->setCellValue("J14", $operator['contact']);

        $sheet->setCellValue("D14", $id);

        // Выводим содержимое файла
        $objWriter = new \PHPExcel_Writer_Excel5($xls);
        $orderFileName = "generated_orders/order_" . $id . ".xlsx";
        $objWriter->save($orderFileName);
        $orderFileName = "../generated_orders/order_" . $id . ".xlsx";

        return $this->render('order', [
            'file' => $orderFileName,
            'list' => $equipment_list,
            'model' => $model,
            'author' => $author,
            'user' => $operator,
            'status' => $order['orderStatus']->title,
            'verdict' => $order['orderVerdict']->title,
            'level' => $order['orderLevel']->title,
            'tasks' => $tasks,
            'stageIndex' => $stages,
            'taskStages' => $stageIndex,
        ]);
    }


    public function actionReport($id)
    {
        /**
         * [Базовые определения]
         * @var [type]
         */
        $model = $this->findModel($id);

        // Открываем файл
        $xls = \PHPExcel_IOFactory::load('files/report.xlsx');
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();
        // Подписываем лист
        $sheet->setTitle('Наряд');

        /**
         * [Определения с фильтрами]
         * @var [type]
         */
        $order = Orders::find()
            ->select('uuid, title, authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid, startDate, openDate, closeDate, createdAt')
            ->where(['_id' => $id])
            ->one();

        /**
         * [Выборка автора и исполнителя]
         */
        $author = Users::find()->select('_id, name, whoIs, contact')
            ->where(['uuid' => $model->authorUuid])
            ->one();

        $operator = Users::find()->select('_id, name, whoIs, contact')
            ->where(['uuid' => $model->userUuid])
            ->one();

        /**
         * Выборка задач, этапов и операций для определенного наряда
         */
        $stageIndex = [];
        $stages = [];
        $equipments = [];
        $equipments_title = [];
        $equipments_cnt = 0;
        $equipment_list = '';

        $tasks = Task::find()
            ->where(['orderUuid' => $order['uuid']])
            ->all();

        $nRow = 13;
        $nTask = 1;
        $nStage = 1;
        $defectsCount = 0;
        foreach ($tasks as $task) {
            $stageIndex = Stage::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();
            $defects = Defect::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();

            foreach ($defects as $defect) {
                $sheet->setCellValue("C" . ($defectsCount + 41), $defect['defectType']->title . ": " . $defect['comment']);
                $sheet->setCellValue("K" . ($defectsCount + 41), $defect['date']);
                $defectsCount++;
            }
            $sheet->setCellValue("C" . $nRow, $nTask . "." . $task['taskTemplate']->title);

//            $est = 0;
//            for ($i = 0; $i < $equipments_cnt; $i++) {
//                if ($equipments[$i] == $task['equipmentUuid']) {
//                    $est = 1;
//                }
//            }
//            if (!$est) {
//                $equipments[$equipments_cnt] = $task['equipmentUuid'];
//                $equipments_title[$equipments_cnt] = $task['equipment']->title;
//                $equipments_cnt++;
//
//                $equipment_list .= $task['equipment']->title . ', ';
//                $sheet->setCellValue("C" . $nRow, $nTask . "." . $task['taskTemplate']->title .
//                    " (" . $task['equipment']->title . ")");
//            }

            if ($task['taskVerdictUuid']) {
                $sheet->setCellValue("J" . $nRow, $task['taskVerdict']->title);
            }

            if (strtotime($task['startDate']) > 0) {
                $sheet->setCellValue("K" . $nRow, date('Y-m-d H:i', strtotime($task['startDate'])));
            }

            if (strtotime($task['endDate']) > 0) {
                $sheet->setCellValue("L" . $nRow, date('Y-m-d H:i', strtotime($task['endDate'])));
            }

            $nRow++;
            $nTask++;

            foreach ($stageIndex as $stage) {
                $sheet->setCellValue("D" . $nRow, $nStage . "." . $stage['stageTemplate']->title);
                $est = 0;
                for ($i = 0; $i < $equipments_cnt; $i++) {
                    if ($equipments[$i] == $stage['equipmentUuid']) {
                        $est = 1;
                        $sheet->setCellValue("D" . $nRow, $nStage . "." .
                            $stage['stageTemplate']->title . " (" . $equipments_title[$i] . ")");
                    }
                }
                if (!$est) {
                    $equipments[$equipments_cnt] = $stage['equipmentUuid'];
                    $equipments_title[$equipments_cnt] = $stage['equipment']->title;
                    $equipments_cnt++;
                    $equipment_list .= $stage['equipment']->title . ', ';
                    $sheet->setCellValue("D" . $nRow, $nStage . "." .
                        $stage['stageTemplate']->title . " (" . $stage['equipment']->title . ")");
                }

                $operation_sum = 0;
                $operations = Operation::find()
                    ->where(['stageUuid' => $stage['uuid']])
                    ->all();

                foreach ($operations as $operation) {
                    if ($operation['operationTemplate']) {
                        $operation_sum += $operation['operationTemplate']->normative;
                    }
                }
                if ($stage['stageVerdict']) {
                    $sheet->setCellValue("J" . $nRow, $stage['stageVerdict']->title);
                }

                if (strtotime($task['startDate']) > 0) {
                    $sheet->setCellValue("K" . $nRow, date('m-d H:i', strtotime($stage['startDate'])));
                }

                if (strtotime($task['endDate']) > 0) {
                    $sheet->setCellValue("L" . $nRow, date('m-d H:i', strtotime($stage['endDate'])));
                }

                $nRow++;
                $nStage++;
            }
            $nStage = 1;
        }


        if (strlen($order['title']) > 0) {
            $sheet->setCellValue("E6", $order['orderStatus']->title);
            $sheet->setCellValue("J6", $order['orderVerdict']->title);
            $sheet->setCellValue("I1", $id);
            $sheet->setCellValue("E3", $order['title']);
            $sheet->setCellValue("E4", $order['startDate']);
            if ($order['closeDate'] != "0000-00-00 00:00:00")
                $sheet->setCellValue("J4", $order['closeDate']);
            $sheet->setCellValue("F11", $order['createdAt']);
        }

        $sheet->setCellValue("E8", $author['name']);
        $sheet->setCellValue("E48", $author['name']);
        $sheet->setCellValue("J8", $author['whoIs']);
        $sheet->setCellValue("E10", $operator['name']);
        $sheet->setCellValue("E49", $operator['name']);
        $sheet->setCellValue("J10", $operator['whoIs']);
        $sheet->setCellValue("J11", $operator['contact']);

        $sheet->setCellValue("D11", $id);

        // Выводим содержимое файла
        $objWriter = new \PHPExcel_Writer_Excel5($xls);
        $orderFileName = "generated_reports/report_" . $id . ".xlsx";
        $objWriter->save($orderFileName);
        $orderFileName = "../generated_reports/report_" . $id . ".xlsx";

        return $this->render(
            'report',
            [
                'file' => $orderFileName,
                'list' => $equipment_list,
                'model' => $model,
                'author' => $author,
                'user' => $operator,
                'status' => $order['orderStatus']->title,
                'tasks' => $tasks,
                'stageIndex' => $stages,
                'taskStages' => $stageIndex,
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

    public function actionList()
    {
        /**
         * [Базовые определения]
         * @var [type]
         */
        $model = 'Test';

        return $this->render('list', [
            'model' => $model,
        ]);
    }

    public function actionGenerate()
    {
        $model = new Orders();

        $model->attemptCount = 0;
        $model->updated = 0;

        // return var_dump(Yii::$app->user->identity->attributes['email']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/task/generate');
        } else {
            return $this->render('generate', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Orders();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MainFunctions::register('Создан наряд ' . $model->title);
            return $this->redirect(['table', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        MainFunctions::register('Удален наряд ' . $this->findModel($id)->title);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Displays a task tree view for all users and task types
     * @return mixed
     */
    public function actionTree()
    {
        $treeUsers[] = '';
        $treeTypes[] = '';
        $taskTemplatesType[] = '';
        $treeUsersCnt[] = '';
        $treeTypesCnt[] = '';

        $customTree = array();

        $allUsers = Users::find()->select('*')->all();
        $allTaskTypes = TaskType::find()->select('*')->all();

        $taskStatus_completed = TaskStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();

        // в данном случае статус один
        if (!empty($taskStatus_completed[0]['uuid'])) {
            $taskStatus_completed_uuid = $taskStatus_completed[0]['uuid'];
        } else {
            $taskStatus_completed_uuid = '';
        }

        $taskStatus_uncompleted = TaskStatus::find()->select('uuid')
            ->where(['title' => ['Не выполнена', 'Новая']])
            ->all();

        // все статусы кроме выполненного
        $cnt = 0;
        $taskStatus_uncompleted_uuid[] = '';
        foreach ($taskStatus_uncompleted as $status) {
            $taskStatus_uncompleted_uuid[$cnt] = $status['uuid'];
            $cnt++;
        }

        $taskCompleted = Task::find()
            ->select('*')
            ->where(['taskStatusUuid' => $taskStatus_completed_uuid])
            ->all();

        $taskCount = 0;
        foreach ($taskCompleted as $task) {
            $customTree[2]['nodes'][$taskCount]['text'] = '[' . date_format(date_create($task['createdAt']),
                    "Y-m-d H:i:s") . '] <a href="\task\view?id=' . $task['_id'] . '">' .
                $task['comment'] . '</a>';
            $customTree[2]['nodes'][$taskCount]['href'] = '\task\view?id=' . $task['_id'];
            $taskCount++;
        }

        $taskUnCompleted = Task::find()
            ->select('*')
            ->where(['taskStatusUuid' => $taskStatus_uncompleted_uuid])
            ->all();

        $taskCount = 0;
        foreach ($taskUnCompleted as $task) {
            $customTree[3]['nodes'][$taskCount]['text'] = '[' . date_format(date_create($task['createdAt']),
                    "Y-m-d H:i:s") . '] <a href="\task\view?id=' . $task['_id'] . '">' . $task['comment'] . '</a>';
            $customTree[3]['nodes'][$taskCount]['href'] = '\task\view?id=' . $task['_id'];
            $taskCount++;
        }

        $taskCompletedCnt = count($taskCompleted);
        $customTree[2]['text'] = 'Выполненные';
        $customTree[2]['icon'] = 'glyphicon glyphicon-ok-circle';
        $customTree[2]['tags'] = [$taskCompletedCnt];

        $taskUnCompletedCnt = count($taskUnCompleted);
        $customTree[3]['text'] = 'Не выполненные';
        $customTree[3]['icon'] = 'glyphicon glyphicon-remove-circle';
        $customTree[3]['tags'] = [$taskUnCompletedCnt];
        //$customTree[3]['backColor'] = '#ff0000';

        $typeCnt = 0;
        foreach ($allTaskTypes as $taskType) {
            $customTree[0]['nodes'][$typeCnt]['text'] = $taskType['title'];
            // нашли все шаблоны нужного типа
            $taskTemplate = TaskTemplate::find()
                ->select('*')
                ->where(['taskTypeUuid' => $taskType['uuid']])
                ->all();

            $cnt = 0;
            $taskTemplates_text = array();
            foreach ($taskTemplate as $template) {
                $taskTemplates_text[$cnt] = $template['uuid'];
                $cnt++;
            }

            $treeTypes = Task::find()
                ->select('*')
                ->where(['taskTemplateUuid' => $taskTemplates_text])
                ->all();

            $customTree[0]['nodes'][$typeCnt]['tags'] = [count($treeTypes)];
            $taskType = 0;
            foreach ($treeTypes as $treeType) {
                $taskTemplate = TaskTemplate::find()
                    ->select('*')
                    ->where(['uuid' => $treeType['taskTemplateUuid']])
                    ->one();
                $customTree[0]['nodes'][$typeCnt]['nodes'][$taskType]['text'] = '[' .
                    date_format(date_create($treeType['createdAt']), "Y-m-d H:i:s") .
                    '] <a href="\task\view?id=' . $treeType['_id'] . '">' . $taskTemplate['title'] . '</a>';
                $taskType++;
            }
            $typeCnt++;
        }

        $userCnt = 0;
        foreach ($allUsers as $user) {
            $customTree[1]['nodes'][$userCnt]['text'] = $user['name'];
            $treeUsers = Orders::find()
                ->select('*')
                ->where(['userUuid' => $user['uuid']])
                ->all();
            $customTree[1]['nodes'][$userCnt]['tags'] = [count($treeUsers)];
            $taskType = 0;
            foreach ($treeUsers as $order) {
                $customTree[1]['nodes'][$userCnt]['nodes'][$taskType]['text'] = '[' .
                    date_format(date_create($order['createdAt']), "Y-m-d H:i:s") .
                    '] <a href="\orders\view?id=' . $order['_id'] . '">' . $order['title'] . '</a>';
                $taskType++;
            }
            $userCnt++;
        }

        $userCnt = count($allUsers);
        $taskTypeCnt = count($allTaskTypes);

        $customTree[0]['text'] = 'Задачи по типам';
        $customTree[0]['icon'] = 'glyphicon glyphicon-menu-hamburger';
        $customTree[0]['tags'] = [$taskTypeCnt];

        $customTree[1]['text'] = 'Наряды по персоналу';
        $customTree[1]['icon'] = 'glyphicon glyphicon-user';
        $customTree[1]['tags'] = [$userCnt];

        $fullTree = array();

        $treeOrders = Orders::find()
            ->select('*')
            ->orderBy(['createdAt' => SORT_DESC])
            ->all();
        $ordersCount = 0;
        foreach ($treeOrders as $order) {
            $status = OrderStatus::find()
                ->select('*')
                ->where(['uuid' => $order['orderStatusUuid']])
                ->one();
            $author = Users::find()
                ->select('*')
                ->where(['uuid' => $order['userUuid']])
                ->one();
            $fullTree[$ordersCount]['text'] = '<a href="\orders\view?id=' . $order['_id'] . '">' .
                $order['title'] . '</a> [' . $author['name'] . ', ' . $order['startDate'] . ']';
            $treeTasks = Task::find()
                ->select('*')
                ->where(['orderUuid' => $order['uuid']])
                ->all();
            $fullTree[$ordersCount]['tags'] = [$status['title'], count($treeTasks)];
            $tasksCount = 0;
            foreach ($treeTasks as $task) {
                $taskTemplate = TaskTemplate::find()
                    ->select('*')
                    ->where(['uuid' => $task['taskTemplateUuid']])
                    ->one();
                if ($task['startDate'] == '0000-00-00 00:00:00') {
                    $fullTree[$ordersCount]['nodes'][$tasksCount]['text'] = '<a href="\task\view?id=' . $task['_id'] . '">'
                        . $taskTemplate['title'] . '</a> ["Не начинался"] ';
                } else {
                    $fullTree[$ordersCount]['nodes'][$tasksCount]['text'] = '<a href="\task\view?id=' . $task['_id'] . '">'
                        . $taskTemplate['title'] . '</a> [' . $task['startDate'] . ']';
                }

                $fullTree[$ordersCount]['nodes'][$tasksCount]['href'] = '\task\view?id=' . $task['_id'];
                $treeStages = Stage::find()
                    ->select('*')
                    ->where(['taskUuid' => $task['uuid']])
                    ->all();
                $fullTree[$ordersCount]['nodes'][$tasksCount]['tags'] = [count($treeStages)];

                $stagesCount = 0;
                foreach ($treeStages as $stage) {
                    $equipment = Equipment::find()
                        ->select('*')
                        ->where(['uuid' => $stage['equipmentUuid']])
                        ->one();
                    $stageTemplate = StageTemplate::find()
                        ->select('*')
                        ->where(['uuid' => $stage['stageTemplateUuid']])
                        ->one();
                    $fullTree[$ordersCount]['nodes'][$tasksCount]['nodes'][$stagesCount]['text'] =
                        Html::a($stageTemplate['title'], ['task-stage/view', 'id' => $stage['_id']]) .
                        ' [' . $stage['startDate'] . ', ' . $equipment['title'] . ' (' . $equipment['inventoryNumber'] . ')]';
                    $treeOperations = Operation::find()
                        ->select('*')
                        ->where(['stageUuid' => $stage['uuid']])
                        ->all();
                    $fullTree[$ordersCount]['nodes'][$tasksCount]['nodes'][$stagesCount]['tags'] = [count($treeOperations)];

                    $operationsCount = 0;
                    foreach ($treeOperations as $operation) {
                        $operationTemplate = OperationTemplate::find()
                            ->select('*')
                            ->where(['uuid' => $operation['operationTemplateUuid']])
                            ->one();
                        $fullTree[$ordersCount]['nodes'][$tasksCount]['nodes'][$stagesCount]['nodes'][$operationsCount]['text'] =
                            Html::a($operationTemplate['title'], ['operation/view', 'id' => $operation['_id']]) .
                            ' [' . $operation['startDate'] . ' - ' . $operation['endDate'] . ']';
                        $operationsCount++;
                    }
                    $stagesCount++;
                }
                $tasksCount++;
            }
            $ordersCount++;
        }
        // var_dump($fullTree);

        return $this->render('tree', [
            'customTree' => $customTree,
            'fullTree' => $fullTree
        ]);
    }

    public function actionCopy()
    {
        $id = $_POST['event_id'];
        $start = $_POST['event_start'];
        //$id = 1;
        //$start='2017-10-13T11:39:00';
        $max = Orders::find()
            ->select('max(_id)')
            ->scalar();
        $order = Orders::find()
            ->select('*')
            ->where(['_id' => $id])
            ->one();

        if ($order) {
            $clone = new Orders;
            var_dump($order->attributes);
            $clone->attributes = $order->attributes;
            $clone->_id = $max + 1;
            $clone->uuid = (new MainFunctions)->GUID();
            $clone->startDate = $start;
            var_dump($clone);
            $clone->save();
            //(new MainFunctions)->logs('copyOrder()');
            OrdersController::copyOrder($id, $clone->uuid);
        }
    }

    public function actionRemove()
    {
        $id = $_POST['event_id'];
        //$id=25;
        $order = Orders::find()
            ->select('*')
            ->where(['_id' => $id])
            ->one();

        if ($order) {
            OrdersController::deleteOrder($id);
            $order->delete();
        }
    }

    public function actionMove()
    {
        $id = $_POST['event_id'];
        $start = $_POST['event_start'];
        //$id=1;
        //$start='2017-10-13T11:39:00';
        $order = Orders::find()
            ->select('*')
            ->where(['_id' => $id])
            ->one();
        if ($order) {
            var_dump($order);
            $order['startDate'] = $start;
            $order->save();
        }
    }


    public function actionCalendar()
    {
        $events = [];

        $orders = Orders::find()
            ->select('_id, title, authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid, startDate, closeDate')
            ->asArray()
            ->all();
        foreach ($orders as $order) {
            $user = Users::find()
                ->select('name')
                ->where(['uuid' => $order['userUuid']])
                ->asArray()
                ->one();
            $status = OrderStatus::find()
                ->select('title')
                ->where(['uuid' => $order['orderStatusUuid']])
                ->asArray()
                ->one();
            $event = new Event();
            $event->id = $order['_id'];
            $event->title = '[' . $user['name'] . '] ' . $order['title'];
            $event->start = $order['startDate'];
            if ($status['title'] == 'Новый')
                $event->backgroundColor = '#aaaaaa';
            if ($status['title'] == 'Выполнен')
                $event->backgroundColor = '#009911';
            if ($status['title'] == 'Не выполнен' || $status['title'] == 'Отменен')
                $event->backgroundColor = '#ff1100';

            if ($order['closeDate'] != '0000-00-00 00:00:00')
                $event->end = $order['closeDate'];
            $event->url = '/orders/' . $order['_id'];
            $event->color = '#333333';
            $events[] = $event;
        }

        return $this->render('calendar', [
            'events' => $events
        ]);
    }

    function copyOrder($id, $order_uuid)
    {
        $orders = new Orders();
        $order = $orders::find()
            ->select('uuid, authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid')
            ->where(['_id' => $id])
            ->one();

        $tasks = Task::find()
            ->where(['orderUuid' => $order['uuid']])
            ->all();

        foreach ($tasks as $key => $task) {
            $model = new Task();
            $max = $model::find()
                ->select('max(_id)')
                ->scalar();
            $clone = new Task();
            $clone->attributes = $task->attributes;
            $clone->_id = $max + 1;
            $clone->orderUuid = $order_uuid;
            $clone->uuid = (new MainFunctions)->GUID();
            $task_uuid = $clone->uuid;
            var_dump($clone);
            $clone->save();

            $stages = Stage::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();
            foreach ($stages as $stage) {
                $max = Stage::find()
                    ->select('max(_id)')
                    ->scalar();
                $clone = new Stage();
                $clone->attributes = $stage->attributes;
                $clone->_id = $max + 1;
                $clone->taskUuid = $task_uuid;
                $clone->uuid = (new MainFunctions)->GUID();
                $stageUuid = $clone->uuid;
                $clone->save();

                $operations = Operation::find()
                    ->where(['stageUuid' => $stage['uuid']])
                    ->all();
                foreach ($operations as $operation) {
                    $model = new Operation();
                    $max = $model::find()
                        ->select('max(_id)')
                        ->scalar();
                    $clone = new Operation();
                    $clone->attributes = $operation->attributes;
                    $clone->_id = $max + 1;
                    $clone->stageUuid = $stageUuid;
                    $clone->uuid = (new MainFunctions)->GUID();
                    $clone->save();
                }
            }
        }
    }

    function deleteOrder($id)
    {
        $order = Orders::find()
            ->select('uuid, authorUuid, userUuid, orderStatusUuid, orderVerdictUuid, orderLevelUuid')
            ->where(['_id' => $id])
            ->one();

        $tasks = Task::find()
            ->where(['orderUuid' => $order['uuid']])
            ->all();

        foreach ($tasks as $key => $task) {
            //echo 'task '.$task['uuid'].'<br/>';
            $stages = Stage::find()
                ->where(['taskUuid' => $task['uuid']])
                ->all();
            foreach ($stages as $stage) {
                //echo 'stage '.$stage['uuid'].'<br/>';
                $operations = Operation::find()
                    ->where(['stageUuid' => $stage['uuid']])
                    ->all();
                foreach ($operations as $operation) {
                    //echo 'operation '.$operation['uuid'].'<br/>';
                    $operation->delete();
                }

                $stage->delete();
            }

            $task->delete();
        }
    }

    /**
     * Details for Order model. List all tasks.
     * @return mixed
     */

    public function actionOrderDetails()
    {
        $tasks = Task::find()
//            ->where(['orderUuid' => $model['uuid']])
            ->asArray()
            ->all();

        return $this->render(
            'order-details',
            [
                'tasks' => $tasks
            ]
        );
    }

}
