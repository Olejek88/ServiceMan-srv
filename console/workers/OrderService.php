<?php

namespace console\workers;

use common\components\MainFunctions;
use common\components\OrderFunctions;
use common\models\Equipment;
use common\models\EquipmentStage;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\OrderVerdict;
use common\models\Task;
use common\models\TaskCron;
use common\models\TaskEquipmentStage;
use common\models\TaskStatus;
use common\models\TaskTemplate;
use common\models\TaskVerdict;
use common\models\Users;
use Cron\CronExpression;
use DateTime;
use Exception;
use inpassor\daemon\Worker;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class OrderService
 *
 * Класс читает таблицу задач и формирует наряды исходя из периодичности
 *
 * @category Category
 * @package  Console\workers
 * @author   Oleg Ivanov <olejek8@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class  OrderService extends Worker
{
    const LOG_ID = "order";

    public $active = true;
    public $maxProcesses = 1;
    public $delay = 7200;

    public function run()
    {
        $this->log('[' . self::LOG_ID . '] start orders worker');
        $database_name = Yii::$app->params['database_name'];
        //$config = Yii::$app->getComponents(true);
        foreach ($database_name as $database) {
            //$this->log('[' . self::LOG_ID . '] ['.$database.'] open connection to '.$config[$database]['dsn']);
            Yii::$app->set('db', Yii::$app->$database);
            if (Yii::$app->db->schema->getTableSchema('service') != null) {
                if (WorkerFunctions::getServiceActive('OrderService') != WorkerConstants::ACTIVE)
                    continue;
                WorkerFunctions::setServiceLastStart('OrderService');
                //$this->log('[' . self::LOG_ID . '] ['.$database.'] open connection to '.$config[$database]['dsn'].' success');
                // запускаем процесс проверок
                $this->log('[' . self::LOG_ID . '] check tasks');
                $this->checkNewTask();
                WorkerFunctions::setServiceLastStop('OrderService');
            }
            //$this->log('[' . self::LOG_ID . '] close');
            Yii::$app->db->close();
        }
    }

    /**
     * 1. читаем таблицу задач
     * 2. упаковываем их в наряд
     * @return int
     */
    public function check()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('task_equipment_stage');
        if ($tableSchema != null) {
            $tasks = TaskEquipmentStage::find()->select('*')
                ->all();
            foreach ($tasks as $task) {
                if ($task['period']) {
                    $taskCron = TaskCron::find()->where(['taskUuid' => $task['taskTemplateUuid']])->one();
                    $cron = CronExpression::factory($task['period']);
                    $cron->isDue();
                    if ($taskCron == null) {
                        $taskCron = new TaskCron();
                        $taskCron->status = TaskCron::STATUS_WAITING;
                        $taskCron->taskUuid = $task['taskTemplateUuid'];
                        $taskCron->last_execution_date = '0';
                        $taskCron->next_execution_date = $cron->getNextRunDate()->format("Y-m-d H:i:s");
                        $this->log($cron->getNextRunDate()->format("Y-m-d H:i:s"));
                        if (!$taskCron->save()) {
                            $this->log('errors: ' . json_encode($taskCron->errors));
                            return WorkerConstants::FAILED;
                        }
                        WorkerFunctions::setServiceMessage('OrderService',
                            WorkerConstants::TYPE_INFO, WorkerConstants::MESSAGE_NEW_TASK_CRON_CREATED);
                    }
                    $currentTime = time();
                    // сработает на запись только если время пришло
                    // это текущее время
                    $taskCron->last_execution_date = date("Y-m-d H:i:s", $currentTime);

                    $this->log($taskCron->status . '] ' . $task['taskTemplate']->title . ' ' .
                        $taskCron->next_execution_date . ' ' .
                        $taskCron->last_execution_date);
                    // оно больше времени следующего запуска
                    if ($taskCron->last_execution_date >= $taskCron->next_execution_date &&
                        $taskCron->status == TaskCron::STATUS_WAITING) {
                        $taskCron->status = TaskCron::STATUS_RUNNING;
                        $taskCron->save();
                        $result = $this->addTaskToOrder($task);
                        if ($result == WorkerConstants::SUCCESS) {
                            sleep(1);
                            $cron = CronExpression::factory($task['period']);
                            //$taskCron->last_execution_date = date("Y-m-d H:i:s",$currentTime);
                            $taskCron->next_execution_date = $cron->getNextRunDate()->format("Y-m-d H:i:s");
                            //$this->log($taskCron->status.'] new='.
                            //    $cron->getNextRunDate()->format('Y-m-d H:i:s'));
                            $taskCron->status = TaskCron::STATUS_WAITING;
                            if (!$taskCron->save()) {
                                $this->log('errors: ' . json_encode($taskCron->errors));
                                return WorkerConstants::FAILED;
                            }
                            WorkerFunctions::setServiceMessage('OrderService',
                                WorkerConstants::TYPE_INFO, WorkerConstants::MESSAGE_NEW_TASK_CRON_CREATED);
                        }
                    }
                }
            }
            //return self::sendMessageToService($dsn, $to, $data);
        }
        return WorkerConstants::SUCCESS;
    }

    /**
     * 1. читаем таблицу задач
     * 2. упаковываем их в наряд
     * @return string
     */
    public function checkNewTask()
    {
        $return = "";
        date_default_timezone_set('Asia/Yekaterinburg');
        $this->log('');
        $tableSchema = Yii::$app->db->schema->getTableSchema('task_equipment_stage');
        if ($tableSchema != null) {
            $equipmentStages = EquipmentStage::find()
                ->select('*')
                ->all();
            foreach ($equipmentStages as $equipmentStage) {
                $taskEquipmentStage = TaskEquipmentStage::find()
                    ->select('*')
                    ->where(['equipmentStageUuid' => $equipmentStage['uuid']])
                    ->andWhere(['taskTemplateUuid' => TaskTemplate::DEFAULT_TASK])
                    ->one();
                $ret['result'] = null;
                $next_date = null;
                if ($taskEquipmentStage) {
                    $period = $taskEquipmentStage["period"];
                    try {
                        $last = new DateTime($taskEquipmentStage["last_date"]);
                        $cron = CronExpression::factory($period);
                        foreach ($cron->getMultipleRunDates(10, $last, false) as $date) {
                            $currentDate = date('Y-m-d');
                            $return .= 'check: ' . $currentDate . ' - ' . $date->format('Y-m-d h') . '<br/>';
                            if ($currentDate == $date->format('Y-m-d')) {
                                $currentUser = Users::find()->one();
                                $equipments = Equipment::find()->where(['equipmentModelUuid' => $equipmentStage['equipmentModelUuid']])->all();
                                foreach ($equipments as $equipment) {
                                    $return .= 'new: ' . $equipmentStage['equipmentModel']['title'].' '.$equipmentStage['uuid'] . ' ' . $equipment['title'] . '<br/>';
                                    $ret = OrderFunctions::createOrder($equipment['uuid'], $currentUser,
                                        $equipmentStage['stageOperation']['stageTemplateUuid'], null);
                                    $return .= $ret['message'];
                                }
                                $next_date = $cron->getNextRunDate($currentDate);
                            }
                            $return .= '<br/>';
                        }
                        if ($ret['result'] && $next_date) {
                            echo '<br/>--'.strtotime($last->format('Y/m/d H:i:s')).' '.strtotime($next_date->format('Y/m/d H:i:s')).'<br/>';
                            $taskEquipmentStage['last_date'] = $next_date->format('Y-m-d H:i:s');
                            $taskEquipmentStage->save();
                            echo json_encode($taskEquipmentStage->errors);
                        }
                    } catch (Exception $e) {
                        echo 'error!\n';
                    }
                }

            }
            /*                $accountUser = Yii::$app->user->identity;
                            $currentUser = Users::findOne(['userId' => $accountUser['id']]);
                            $equipment = Equipment::findOne(['_id' => $_POST["Equipment"]['_id']]);
                            if ($equipment) {
                                $return = OrderFunctions::createOrder($equipment['uuid'], $currentUser, $equipmentStageUuid, null);
                                if ($return['result'] == null)
                                    return $return['message'];
                                else
                                    return false;
                            }
                        }*/
        }
        return $return;
    }

    /**
     * формируем задачу из шаблона и добавляем ее к уже открытому наряду, если такового нет - создаем его
     * @param $task_equipment_stage ActiveRecord
     * @param $date
     * @return int
     */
    public function addNewTask($task_equipment_stage, $date)
    {
        $this->log('add task to order');
        $order = Orders::find()->where(['orderStatusUuid' => OrderStatus::FORMING])
            ->orderBy('createdAt DESC')->one();
        $user = Users::find()->where(['active' => 1])
            ->orderBy('createdAt DESC')->one();
        if (!$order) {
            $this->log('create new order');
            $order = new Orders();
            $order->uuid = WorkerFunctions::GUID();
            $order->startDate = date("Y-m-d H:i:s");
            $order->comment = 'Наряд создан автоматически';
            $order->title = 'Плановый наряд для ' . $task_equipment_stage['equipmentStage']['equipment']->title;
            $order->orderStatusUuid = OrderStatus::FORMING;
            $order->userUuid = $user['uuid'];
            $order->attemptCount = 0;
            $order->authorUuid = Users::USER_SYSTEM;
            $order->reason = 'По времени';
            $order->orderVerdictUuid = OrderVerdict::UNKNOWN;
            $this->log('attempt to create order');
            if (!$order->save()) {
                $this->log('Ошибка автоматического создания наряда ' . json_encode($order->errors));
                WorkerFunctions::setServiceMessage('OrderService',
                    WorkerConstants::TYPE_ERROR, WorkerConstants::MESSAGE_ERROR_NEW_ORDER_CREATED);
                return WorkerConstants::FAILED;
            }
        }
        $this->log('attempt to create task');
        $task = new Task();
        $task->uuid = WorkerFunctions::GUID();
        //$task->equipmentUuid = $task_equipment_stage['equipmentStage']['equipment']->uuid;
        $task->taskTemplateUuid = $task_equipment_stage['taskTemplate']->uuid;
        $task->orderUuid = $order['uuid'];
        $task->comment = 'Задача создана автоматически';
        //$task->startDate = $order['startDate'];
        $task->taskStatusUuid = TaskStatus::NEW_TASK;
        $task->taskVerdictUuid = TaskVerdict::NOT_DEFINED;
        $task->prevCode = 0;
        $task->nextCode = 0;
        if (!$task->save()) {
            $this->log('Ошибка автоматического создания задачи' . json_encode($task->errors));
            WorkerFunctions::setServiceMessage('OrderService',
                WorkerConstants::TYPE_ERROR, WorkerConstants::MESSAGE_ERROR_NEW_TASK_CREATED);
            return WorkerConstants::FAILED;
        }
        $this->log('successfully created order and add task');
        WorkerFunctions::setServiceMessage('OrderService',
            WorkerConstants::TYPE_ERROR, WorkerConstants::MESSAGE_NEW_TASK_CREATED);
        return WorkerConstants::SUCCESS;
    }


    /**
     * формируем задачу из шаблона и добавляем ее к уже открытому наряду, если такового нет - создаем его
     * @param $task_equipment_stage ActiveRecord
     * @return int
     */
    public function addTaskToOrder($task_equipment_stage)
    {
        $this->log('add task to order');
        $order = Orders::find()->where(['orderStatusUuid' => OrderStatus::FORMING])
            ->orderBy('createdAt DESC')->one();
        $user = Users::find()->where(['active' => 1])
            ->orderBy('createdAt DESC')->one();
        if (!$order) {
            $this->log('create new order');
            $order = new Orders();
            $order->uuid = WorkerFunctions::GUID();
            $order->startDate = date("Y-m-d H:i:s");
            $order->comment = 'Наряд создан автоматически';
            $order->title = 'Плановый наряд для ' . $task_equipment_stage['equipmentStage']['equipment']->title;
            $order->orderStatusUuid = OrderStatus::FORMING;
            $order->userUuid = $user['uuid'];
            $order->attemptCount = 0;
            $order->authorUuid = Users::USER_SYSTEM;
            $order->reason = 'По времени';
            $order->orderVerdictUuid = OrderVerdict::UNKNOWN;
            $this->log('attempt to create order');
            if (!$order->save()) {
                $this->log('Ошибка автоматического создания наряда ' . json_encode($order->errors));
                WorkerFunctions::setServiceMessage('OrderService',
                    WorkerConstants::TYPE_ERROR, WorkerConstants::MESSAGE_ERROR_NEW_ORDER_CREATED);
                return WorkerConstants::FAILED;
            }
        }
        $this->log('attempt to create task');
        $task = new Task();
        $task->uuid = WorkerFunctions::GUID();
        //$task->equipmentUuid = $task_equipment_stage['equipmentStage']['equipment']->uuid;
        $task->taskTemplateUuid = $task_equipment_stage['taskTemplate']->uuid;
        $task->orderUuid = $order['uuid'];
        $task->comment = 'Задача создана автоматически';
        //$task->startDate = $order['startDate'];
        $task->taskStatusUuid = TaskStatus::NEW_TASK;
        $task->taskVerdictUuid = TaskVerdict::NOT_DEFINED;
        $task->prevCode = 0;
        $task->nextCode = 0;
        if (!$task->save()) {
            $this->log('Ошибка автоматического создания задачи' . json_encode($task->errors));
            WorkerFunctions::setServiceMessage('OrderService',
                WorkerConstants::TYPE_ERROR, WorkerConstants::MESSAGE_ERROR_NEW_TASK_CREATED);
            return WorkerConstants::FAILED;
        }
        $this->log('successfully created order and add task');
        WorkerFunctions::setServiceMessage('OrderService',
            WorkerConstants::TYPE_ERROR, WorkerConstants::MESSAGE_NEW_TASK_CREATED);
        return WorkerConstants::SUCCESS;
    }

    /**
     * функция sendMessageToService отправляет сообщение различным сервисам
     *
     * @param String $dsn
     * @param String $to
     * @param String $data
     * @return int
     */
    function sendMessageToService($dsn, $to, $data)
    {
        return WorkerConstants::SUCCESS;
    }
}
