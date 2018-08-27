<?php

namespace backend\controllers;

use common\models\Defect;
use common\models\Equipment;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\Users;
use yii\helpers\Html;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;
use common\models\Operation;
use common\models\OperationTemplate;
use common\models\OperationStatus;
use common\models\Stage;
use common\models\StageTemplate;
use common\models\StageStatus;
use common\components\MainFunctions;

use common\models\Task;
use common\models\TaskStatus;
use common\models\TaskTemplate;


class AnalyticsController extends Controller
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

    public function init() {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    public function getBar($time, $normative)
    {
        $difference = 100;
        if ($normative>0)
            $difference = intval(($time-$normative)/$normative);
        $fullTree = '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="'.$difference.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$difference.'%; background-color: ';
        if ($difference>100) $fullTree.='#ee2222';
        if ($difference>80 && $difference<=100) $fullTree.='#2222dd';
        if ($difference<=80) $fullTree.='#22dd22';
        $fullTree.=';"></div><span class="progress-completed">'.$difference.'%</span></div>';
        return $fullTree;
    }

    public function getBar2($end, $start, $normative)
    {
        $difference = 100;
        if ($normative>0)
            $difference = intval((MainFunctions::getOperationLength($start, $end,$normative*50)-$normative)*100/$normative);
        $fullTree = '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="'.$difference.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$difference.'%; background-color: ';
        if ($difference>100) $fullTree.='#ee2222;';
        if ($difference>80 && $difference<=100) $fullTree.='#2222dd;';
        if ($difference<=80) $fullTree.='#22dd22;';
        $fullTree.='"></div><span class="progress-completed" ';
        if ($difference>80) $fullTree.=' style="color: #ffffff"';
        $fullTree.='>'.$difference.'%</span></div>';
        return $fullTree;
    }

    public function actionTemplate()
    {
        $taskTemplatesType[]='';
        $treeUsersCnt[]='';
        $treeTypesCnt[]='';

        $taskStatus_completed = TaskStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();
        if (!empty($taskStatus_completed[0]['uuid']))
            $taskStatus_completed_uuid = $taskStatus_completed[0]['uuid'];
        else
            $taskStatus_completed_uuid = '';

        $stageStatus_completed = StageStatus::find()->select('*')
            ->where(['title' => 'Выполнен'])
            ->all();
        if (!empty($stageStatus_completed[0]['uuid']))
            $stageStatus_completed_uuid = $stageStatus_completed[0]['uuid'];
        else
            $stageStatus_completed_uuid = '';

        $operationStatus_completed = OperationStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();
        if (!empty($operationStatus_completed[0]['uuid']))
            $operationStatus_completed_uuid = $operationStatus_completed[0]['uuid'];
        else
            $operationStatus_completed_uuid = '';

        $taskTemplates = TaskTemplate::find()
            ->select('*')
            ->all();

        $taskSumTime=0;
        $taskSumNormative=0;
        $stageSumTime=0;
        $stageSumNormative=0;
        $operationSumTime=0;
        $operationSumNormative=0;

        $taskTemplatesCount = 0;
        foreach ($taskTemplates as $taskTemplate) {
            $treeTasks = Task::find()
                ->select('*')
                ->where(['taskTemplateUuid' => $taskTemplate['uuid']])
                ->all();
            $sumTasks = count($treeTasks);
            $treeTasks = Task::find()
                ->select('*')
                ->where(['taskTemplateUuid' => $taskTemplate['uuid']])
                ->andWhere(['taskStatusUuid' => $taskStatus_completed_uuid])
                ->orderBy(['startDate' => SORT_DESC])
                ->all();
            $sumTasksCompleted = count($treeTasks);

            // в версии 3 шаблон задачи никак не связан с оборудованием
            $sumTime = 0;
            $avgTime = 0;
            $countTime = 0;
            $difference = 0;
            $lastTime = '';
            foreach ($treeTasks as $task) {
                $taskLength = MainFunctions::getOperationLength($task['startDate'],$task['endDate'],3600*48);
                if ($taskLength>0) {
                    if ($lastTime == '') $lastTime = $task['startDate'];
                    $sumTime += $taskLength;
                    $countTime++;
                }
            }
            $taskSumTime+=$sumTime;
            $taskSumNormative+=$taskTemplate['normative'];

            if ($countTime > 0) {
                $avgTime = $sumTime / $countTime;
                if ($taskTemplate['normative'] > 0)
                    $difference = ($avgTime - $taskTemplate['normative']) * 100 / $taskTemplate['normative'];
            }

            $fullTree[0]["children"][$taskTemplatesCount]["title"] =
                Html::a($taskTemplate['title'], ['task-template/view', 'id' => $taskTemplate['_id']]);
            $fullTree[0]["children"][$taskTemplatesCount]["date"] = $lastTime;
            //$fullTree[0]["children"][$taskTemplatesCount]["equipment"] = $equipment['title'];
            if ($sumTasks>0)
                $fullTree[0]["children"][$taskTemplatesCount]["quantity"] = $sumTasksCompleted . ' / ' . $sumTasks;
            $fullTree[0]["children"][$taskTemplatesCount]["sumTime"] = $sumTime;
            $fullTree[0]["children"][$taskTemplatesCount]["avgTime"] = number_format($avgTime,1);
            $fullTree[0]["children"][$taskTemplatesCount]["normative"] = $taskTemplate['normative'];
            if ($avgTime>0)
                $fullTree[0]["children"][$taskTemplatesCount]["difference"] = number_format($avgTime - $taskTemplate['normative'],0) . ' (' . number_format($difference,1) . '%)';
            if ($avgTime > 0 && $difference > 20)
                $fullTree[0]["children"][$taskTemplatesCount]["hint"] = 'Рекомендуется увеличить до ' . number_format($avgTime,0) . ' секунд';
            if ($avgTime > 0 && $difference < -20)
                $fullTree[0]["children"][$taskTemplatesCount]["hint"] = 'Рекомендуется уменьшить до ' . number_format($avgTime,0) . ' секунд';
            $taskTemplatesCount++;
        }

        $stageTemplates = StageTemplate::find()
            ->select('*')
            ->all();

        $stageTemplatesCount = 0;
        foreach ($stageTemplates as $stageTemplate) {
            $treeTasks = Stage::find()
                ->select('*')
                ->where(['stageTemplateUuid' => $stageTemplate['uuid']])
                ->all();
            $sumTasks = count($treeTasks);
            $treeTasks = Stage::find()
                ->select('*')
                ->where(['stageTemplateUuid' => $stageTemplate['uuid']])
                ->andWhere(['stageStatusUuid' => $stageStatus_completed_uuid])
                ->orderBy(['startDate' => SORT_DESC])
                ->all();
            $sumTasksCompleted = count($treeTasks);

            $sumTime = 0;
            $avgTime = 0;
            $countTime = 0;
            $difference = 0;
            $lastTime = '';
            foreach ($treeTasks as $task) {
                $taskLength = MainFunctions::getOperationLength($task['startDate'],$task['endDate'],3600*48);
                if ($taskLength>0) {
                    if ($lastTime == '') $lastTime = $task['startDate'];
                    $sumTime += $taskLength;
                    $countTime++;
                }
            }
            if ($countTime > 0) {
                $avgTime = $sumTime / $countTime;
                if ($stageTemplate['normative'] > 0)
                    $difference = ($avgTime - $stageTemplate['normative']) * 100 / $stageTemplate['normative'];
            }
            $stageSumTime+=$sumTime;
            $stageSumNormative+=$stageTemplate['normative'];
            $fullTree[1]["children"][$stageTemplatesCount]["title"] =
                Html::a($stageTemplate['title'], ['stage-template/view', 'id' => $stageTemplate['_id']]);
            $fullTree[1]["children"][$stageTemplatesCount]["date"] = $lastTime;
            //$fullTree[1]["children"][$stageTemplatesCount]["equipment"] = $equipment['title'];
            if ($sumTasks>0)
                $fullTree[1]["children"][$stageTemplatesCount]["quantity"] = $sumTasksCompleted . ' / ' . $sumTasks;
            $fullTree[1]["children"][$stageTemplatesCount]["sumTime"] = $sumTime;
            $fullTree[1]["children"][$stageTemplatesCount]["avgTime"] = number_format($avgTime,1);
            $fullTree[1]["children"][$stageTemplatesCount]["normative"] = $stageTemplate['normative'];
            if ($avgTime>0)
                $fullTree[1]["children"][$stageTemplatesCount]["difference"] = number_format($avgTime - $stageTemplate['normative'],0) . ' (' . number_format($difference,1) . '%)';
            if ($avgTime > 0 && $difference > 20)
                $fullTree[1]["children"][$stageTemplatesCount]["hint"] = 'Рекомендуется увеличить до ' . number_format($avgTime,0) . ' секунд';
            if ($avgTime > 0 && $difference < -20)
                $fullTree[1]["children"][$stageTemplatesCount]["hint"] = 'Рекомендуется уменьшить до ' . number_format($avgTime,0) . ' секунд';
            $stageTemplatesCount++;
        }

        $operationTemplates = OperationTemplate::find()
            ->select('*')
            ->orderBy(['_id' => SORT_DESC])
            ->all();

        $operationTemplatesCount = 0;
        foreach ($operationTemplates as $operationTemplate) {
            $treeTasks = Operation::find()
                ->select('*')
                ->where(['operationTemplateUuid' => $operationTemplate['uuid']])
                ->all();
            $sumTasks = count($treeTasks);
            $treeTasks = Operation::find()
                ->select('*')
                ->where(['operationTemplateUuid' => $operationTemplate['uuid']])
                ->andWhere(['operationStatusUuid' => $operationStatus_completed_uuid])
                ->orderBy(['startDate' => SORT_DESC])
                ->all();
            $sumTasksCompleted = count($treeTasks);

            $sumTime = 0;
            $avgTime = 0;
            $countTime = 0;
            $difference = 0;
            $lastTime = '';
            foreach ($treeTasks as $task) {
                $taskLength = MainFunctions::getOperationLength($task['startDate'],$task['endDate'],3600*48);
                if ($taskLength>0) {
                    if ($lastTime == '') $lastTime = $task['startDate'];
                    $sumTime += strtotime($task['endDate']) - strtotime($task['startDate']);
                    $countTime++;
                }
            }
            if ($countTime > 0) {
                $avgTime = $sumTime / $countTime;
                if ($operationTemplate['normative'] > 0)
                    $difference = ($avgTime - $operationTemplate['normative']) * 100 / $operationTemplate['normative'];
            }
            $operationSumTime+=$sumTime;
            $operationSumNormative+=$operationTemplate['normative'];

            $fullTree[2]["children"][$operationTemplatesCount]["title"] =
                Html::a($operationTemplate['title'], ['operation-template/view', 'id' => $operationTemplate['_id']]);
            $fullTree[2]["children"][$operationTemplatesCount]["date"] = $lastTime;
            //$fullTree[2]["children"][$operationTemplatesCount]["equipment"] = $equipment['title'];
            if ($sumTasks>0)
                $fullTree[2]["children"][$operationTemplatesCount]["quantity"] = $sumTasksCompleted . ' / ' . $sumTasks;
            $fullTree[2]["children"][$operationTemplatesCount]["sumTime"] = $sumTime;
            $fullTree[2]["children"][$operationTemplatesCount]["avgTime"] = number_format($avgTime,1);
            $fullTree[2]["children"][$operationTemplatesCount]["normative"] = $operationTemplate['normative'];
            if ($avgTime>0)
                $fullTree[2]["children"][$operationTemplatesCount]["difference"] = number_format($avgTime - $operationTemplate['normative'],0) . ' (' . number_format($difference,1) . '%)';
            if ($avgTime > 0 && $difference > 20)
                $fullTree[2]["children"][$operationTemplatesCount]["hint"] = 'Рекомендуется увеличить до ' . number_format($avgTime,0) . ' секунд';
            if ($avgTime > 0 && $difference < -20)
                $fullTree[2]["children"][$operationTemplatesCount]["hint"] = 'Рекомендуется уменьшить до ' . number_format($avgTime,0) . ' секунд';
            $operationTemplatesCount++;
        }

        $fullTree[0]["title"] = 'Задачи ('.$taskTemplatesCount.')';
        $fullTree[0]["hint"] = 'изменение норматива';
        $fullTree[0]["quantity"] = 'вып / всего';
        $fullTree[0]["sumTime"] = $taskSumTime;
        $fullTree[0]["avgTime"] = 'сек.';
        $fullTree[0]["normative"] = $taskSumNormative;
        $fullTree[1]["title"] = 'Этапы задач ('.$stageTemplatesCount.')';
        $fullTree[1]["quantity"] = 'вып / всего';
        $fullTree[1]["sumTime"] = $stageSumTime;
        $fullTree[1]["avgTime"] = 'сек.';
        $fullTree[1]["normative"] = $stageSumNormative;
        $fullTree[1]["hint"] = 'изменение норматива';
        $fullTree[2]["title"] = 'Операции ('.$operationTemplatesCount.')';
        $fullTree[2]["quantity"] = 'вып / всего';
        $fullTree[2]["sumTime"] = $operationSumTime;
        $fullTree[2]["normative"] = $operationSumNormative;
        $fullTree[2]["avgTime"] = 'сек.';
        $fullTree[2]["hint"] = 'изменение норматива';

        //var_dump($fullTree);
        return $this->render('template', [
            'orders' => $fullTree
        ]);
    }

    public function actionUsers()
    {
        $taskTemplatesType[] = '';
        $treeUsersCnt[] = '';
        $treeTypesCnt[] = '';

        $orderStatus_completed = OrderStatus::find()->select('*')
            ->where(['title' => 'Выполнен'])
            ->all();
        if (!empty($orderStatus_completed[0]['uuid'])) {
            $orderStatus_completed_uuid = $orderStatus_completed[0]['uuid'];
        } else {
            $orderStatus_completed_uuid = '';
        }

        $taskStatus_completed = TaskStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();
        if (!empty($taskStatus_completed[0]['uuid'])) {
            $taskStatus_completed_uuid = $taskStatus_completed[0]['uuid'];
        } else {
            $taskStatus_completed_uuid = '';
        }

        $stageStatus_completed = StageStatus::find()->select('*')
            ->where(['title' => 'Выполнен'])
            ->all();
        if (!empty($stageStatus_completed[0]['uuid'])) {
            $stageStatus_completed_uuid = $stageStatus_completed[0]['uuid'];
        } else {
            $stageStatus_completed_uuid = '';
        }

        $operationStatus_completed = OperationStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();
        if (!empty($operationStatus_completed[0]['uuid'])) {
            $operationStatus_completed_uuid = $operationStatus_completed[0]['uuid'];
        } else {
            $operationStatus_completed_uuid = '';
        }

        $operationTemplates = OperationTemplate::find()
            ->select('*')
            ->all();
        $operations = Operation::find()
            ->select('*')
            ->all();
        $stages = Stage::find()
            ->select('*')
            ->all();
        $tasks = Task::find()
            ->select('*')
            ->all();

        $fullTree = array();

        $allUsers = Users::find()->select('*')->all();
        $userCnt = 0;
        foreach ($allUsers as $user) {
            $fullTree[$userCnt]["name"] = $user['name'];
            $fullTree[$userCnt]["who"] = $user['whoIs'];

            $sumDefects = Defect::find()
                ->select('*')
                ->where(['userUuid' => $user['uuid']])
                ->all();
            $fullTree[$userCnt]["defects"] = count($sumDefects);

            $treeOrders = Orders::find()
                ->select('*')
                ->where(['userUuid' => $user['uuid']])
                ->andWhere(['orderStatusUuid' => $orderStatus_completed_uuid])
                ->orderBy(['openDate' => SORT_DESC])
                ->all();
            $sumOrdersCompleted = count($treeOrders);

            $treeOrders = Orders::find()
                ->select('*')
                ->where(['userUuid' => $user['uuid']])
                ->all();
            $cnt = 0;
            $orders_list[] = '';
            foreach ($treeOrders as $order) {
                $orders_list[$cnt] = $order['uuid'];
                $cnt++;
            }

            $sumOrders = count($treeOrders);

            if ($sumOrders > 0) {
                $fullTree[$userCnt]["orders"] = $sumOrdersCompleted . '/' . $sumOrders . ' (' . number_format(100 * $sumOrdersCompleted / $sumOrders, 2) . '%)';
            }

            $sumOperationsCompleted = 0;
            $sumOperations = 0;
            $sumTime = 0;
            $sumNormative = 0;
            $sumStages = 0;
            $sumStagesCompleted = 0;
            $sumTasks = 0;
            $sumTasksCompleted = 0;

            foreach ($treeOrders as $order) {
                $fullTree[$userCnt]["date"] = $order['openDate'];

                foreach ($tasks as $task) {
                    if ($order['uuid'] == $task['orderUuid']) {
                        $sumTasks++;

                        if ($task['taskStatusUuid'] == $taskStatus_completed_uuid)
                            foreach ($stages as $stage) {
                                $sumTasksCompleted++;
                                if ($task['uuid'] == $stage['taskUuid']) {
                                    $sumStages++;
                                    if ($stage['stageStatusUuid'] == $stageStatus_completed_uuid)
                                        foreach ($operations as $operation) {
                                            $sumStagesCompleted++;
                                            if ($stage['uuid'] == $operation['stageUuid']) {
                                                $sumOperations++;
                                                if ($operation['operationStatusUuid'] == $operationStatus_completed_uuid) {
                                                    $sumOperationsCompleted++;
                                                    foreach ($operationTemplates as $operationTemplate) {
                                                        if ($operation['operationTemplateUuid'] == $operationTemplate['uuid']) {
                                                            $sumNormative += $operationTemplate['normative'];
                                                            if (strtotime($operation['endDate']) > 100000 && strtotime($operation['startDate']) > 100000 && (strtotime($operation['endDate']) - strtotime($operation['startDate'])) > 0 && (strtotime($operation['endDate']) - strtotime($operation['startDate'])) < 7200*24) {
                                                                $time = strtotime($operation['endDate']) - strtotime($operation['startDate']);
                                                                $sumTime += $time;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                }
                            }
                    }
                }
            }
            if ($sumTasks > 0) {
                $fullTree[$userCnt]["tasks"] = $sumTasksCompleted . '/' . $sumTasks . ' (' . number_format($sumTasksCompleted * 100 / $sumTasks, 2) . '%)';
            }

            if ($sumStages > 0) {
                $fullTree[$userCnt]["stages"] = $sumStagesCompleted . '/' . $sumStages . ' (' . number_format($sumStagesCompleted * 100 / $sumStages, 2) . '%)';
            }

            if ($sumOperations > 0) {
                $fullTree[$userCnt]["operations"] = $sumOperationsCompleted . '/' . $sumOperations . ' (' . number_format($sumOperationsCompleted * 100 / $sumOperations, 2) . '%)';
            }

            if ($sumOperations > 0) {
                $fullTree[$userCnt]["ctr"] = $sumOperationsCompleted . '/' . $sumOperations . ' (' . number_format($sumOperationsCompleted * 100 / $sumOperations, 2) . '%)';
            } else {
                $fullTree[$userCnt]["ctr"] = "0 %";
            }

            if ($sumNormative > 0) {
                $fullTree[$userCnt]["ctr"] = number_format($sumTime * 100 / $sumNormative, 2);
            }

            $fullTree[$userCnt]["time"] = $sumTime;
            $userCnt++;
        }

        //var_dump($fullTree);
        return $this->render('users', [
            'orders' => $fullTree
        ]);
    }

    /**
     * Displays a schedule for all users
     * @return mixed
     */
    public function actionSchedule()
    {
        $orders = new Orders();
        $users = new Users();
        $allOrders = '';
        $allUsers = $users::find()->select('*')->all();

        $userCnt = count($allUsers);
        $chart="";
        $first=0;

        for ($cnt = 0; $cnt < $userCnt; $cnt++) {
            $userOrders = $orders::find()
                ->select('*')
                ->where(['userUuid' => $allUsers[$cnt]['uuid']])
                ->asArray()
                ->all();
            if ($first>0)
                $chart.=',';
            $chart.="{ taskName: '".$allUsers[$cnt]['name']."',".PHP_EOL;
            $chart.="  id: '".$allUsers[$cnt]['uuid']."'}".PHP_EOL;
            $second=0;
            foreach ($userOrders as $order) {
                if ($second>=0)
                    $chart.=','.PHP_EOL;
                $chart.="{".PHP_EOL;
                $chart.="taskName: '[".$order['_id']."] ";
                $chart.=$order['title']."',".PHP_EOL;
                $chart.="id:'".$order['uuid']."',";
                $chart.="parent: '".$allUsers[$cnt]['uuid']."',";
                $chart.="label: '".$allUsers[$cnt]['uuid']."',";
                $chart.="start: ".(strtotime($order['startDate'])*1000).",";
                $chart.="end: ".(strtotime($order['startDate'])*1000+3600000*24).",";
                $chart.="completed: { amount: 0.0 }";
                $chart.=PHP_EOL."}".PHP_EOL;
                $second++;
            }
            $first++;
        }

        return $this->render('schedule', [
            'chart' => $chart,
            'orders' => $allOrders,
            'users' => $allUsers,
        ]);
    }

    public function actionOrdersCtr()
    {
        $taskTemplatesType[]='';
        $treeUsersCnt[]='';
        $treeTypesCnt[]='';

        // в данном случае статус один
        /*
        if (!empty($orderStatus_completed[0]['uuid']))
            $orderStatus_completed_uuid = $orderStatus_completed[0]['uuid'];
        else
            $orderStatus_completed_uuid = '';
        */
        $treeOrders = Orders::find()
            ->select('*')
            //->where(['orderStatusUuid' => $orderStatus_completed_uuid])
            ->orderBy(['createdAt' => SORT_DESC])
            ->all();

        $fullTree = array();
        $ordersCount=0;
        foreach ($treeOrders as $order) {
            $status = OrderStatus::find()
                ->select('*')
                ->where(['uuid' => $order['orderStatusUuid']])
                ->one();
            $author = Users::find()
                ->select('*')
                ->where(['uuid' => $order['userUuid']])
                ->one();
            $fullTree[$ordersCount]["title"]=$order['title'];
            $fullTree[$ordersCount]["author"]=$author['name'];
            $fullTree[$ordersCount]["orderStatus"]=$status['title'];
            $fullTree[$ordersCount]["openDate"]=$order['openDate'];
            $fullTree[$ordersCount]["closeDate"]=$order['closeDate'];

            $treeTasks = Task::find()
                ->select('*')
                ->where(['orderUuid' => $order['uuid']])
                ->all();
            $tasksCount=0;
            $taskTime=0;
            foreach ($treeTasks as $task) {
                $taskTemplate = TaskTemplate::find()
                    ->select('*')
                    ->where(['uuid' => $task['taskTemplateUuid']])
                    ->one();
                $status = TaskStatus::find()
                    ->select('*')
                    ->where(['uuid' => $task['taskStatusUuid']])
                    ->one();

                $fullTree[$ordersCount]["children"][$tasksCount]["title"]=
                    Html::a($taskTemplate['title'], ['task-template/view', 'id' => $taskTemplate['_id']]);
                $taskTemplate['title'];
                $fullTree[$ordersCount]["children"][$tasksCount]["author"]=$author['name'];
                $fullTree[$ordersCount]["children"][$tasksCount]["orderStatus"]=$status['title'];
                $fullTree[$ordersCount]["children"][$tasksCount]["openDate"]=$task['startDate'];
                $fullTree[$ordersCount]["children"][$tasksCount]["closeDate"]=$task['endDate'];
                $fullTree[$ordersCount]["children"][$tasksCount]["equipment"]='';

                $treeStages = Stage::find()
                    ->select('*')
                    ->where(['taskUuid' => $task['uuid']])
                    ->all();

                $stagesCount=0;
                $stageTime=0;
                foreach ($treeStages as $stage) {
                    $equipment = Equipment::find()
                        ->select('*')
                        ->where(['uuid' => $stage['equipmentUuid']])
                        ->one();
                    $stageTemplate = StageTemplate::find()
                        ->select('*')
                        ->where(['uuid' => $stage['stageTemplateUuid']])
                        ->one();
                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["title"] = $stageTemplate['title'];
                    $treeOperations = Operation::find()
                        ->select('*')
                        ->where(['stageUuid' => $stage['uuid']])
                        ->all();

                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["title"]=
                        Html::a($stageTemplate['title'], ['stage-template/view', 'id' => $stageTemplate['_id']]);
                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["author"]=$author['name'];
                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["orderStatus"]=$stage['stageStatus']->title;
                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["openDate"]=$stage['startDate'];
                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["closeDate"]=$stage['endDate'];
                    $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["equipment"]=$equipment['title'];

                    $operationsCount=0;
                    $operationTime=0;
                    foreach ($treeOperations as $operation) {
                        $operationTemplate = OperationTemplate::find()
                            ->select('*')
                            ->where(['uuid' => $operation['operationTemplateUuid']])
                            ->one();
                        $status = OperationStatus::find()
                            ->select('*')
                            ->where(['uuid' => $operation['operationStatusUuid']])
                            ->one();

                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["title"] = $operationTemplate['title'];
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["orderStatus"] = $status['title'];
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["openDate"] = $operation['startDate'];
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["closeDate"] = $operation['endDate'];
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["equipment"] = "-//-//-";

                        $operationLength = MainFunctions::getOperationLength($operation['startDate'],$operation['endDate'],3600);
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["time"] = $operationLength;
                        if ($operationLength>0) {
                            $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["difference"]
                                = AnalyticsController::getBar2($operation['endDate'], $operation['startDate'], $operationTemplate['normative']);
                        } else {
                            $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["difference"]
                                = AnalyticsController::getBar2(0, 0, $operationTemplate['normative']);
                        }
                        $operationTime+=$operationTemplate['normative'];
                        $stageTime+=$operationTemplate['normative'];
                        $taskTime+=$operationTemplate['normative'];


                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["children"][$operationsCount]["normative"]
                            =$operationTemplate['normative'];
                        $operationsCount++;
                    }
                    if ($operationTime>0)
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["normative"]=$operationTime;
                    else {
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["normative"] = $stageTemplate['normative'];
                        $stageTime+=$stageTemplate['normative'];
                        $taskTime+=$stageTemplate['normative'];
                    }
                    $stageLength = MainFunctions::getOperationLength($stage['startDate'],$stage['endDate'],3600*4);
                    if ($stageLength>0) {
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["time"] = $stageLength;
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["difference"] =
                            AnalyticsController::getBar2 ($stage['endDate'],$stage['startDate'],$stageTemplate['normative']);
                    }
                    else
                        $fullTree[$ordersCount]["children"][$tasksCount]["children"][$stagesCount]["difference"] =
                            AnalyticsController::getBar2 (0,0,$stageTemplate['normative']);
                    $stagesCount++;
                }
                if ($stageTime>0)
                    $fullTree[$ordersCount]["children"][$tasksCount]["normative"] = $stageTime;
                else {
                    $fullTree[$ordersCount]["children"][$tasksCount]["normative"] = $taskTemplate['normative'];
                    $taskTime+=$taskTemplate['normative'];
                }
                $taskLength = MainFunctions::getOperationLength($task['startDate'],$task['endDate'],3600*24);
                if ($taskLength>0) {
                    $fullTree[$ordersCount]["children"][$tasksCount]["time"] = $taskLength;
                    $fullTree[$ordersCount]["children"][$tasksCount]["difference"] =
                        AnalyticsController::getBar2 ($task['endDate'],$task['startDate'],$taskTemplate['normative']);
                }
                $tasksCount++;
            }

            $fullTree[$ordersCount]["normative"]=$taskTime;
            $orderLength = MainFunctions::getOperationLength($order['openDate'],$order['closeDate'],3600*96);
            if ($orderLength>0) {
                $fullTree[$ordersCount]["time"] = $orderLength;
                $fullTree[$ordersCount]["difference"] =
                    AnalyticsController::getBar2 ($order['openDate'],$order['closeDate'],$taskTime);
            }
            else
                $fullTree[$ordersCount]["time"]=0;

            $ordersCount++;
        }
        //var_dump($fullTree);
        return $this->render('orders-ctr', [
            'orders' => $fullTree
        ]);
    }
}
