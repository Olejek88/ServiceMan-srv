<?php

namespace console\workers;

use common\components\MainFunctions;
use common\models\Organization;
use common\models\Task;
use common\models\TaskTemplateEquipment;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\WorkStatus;
use inpassor\daemon\Worker;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class TaskService
 *
 * Класс читает таблицу задач исходя из периодичности
 *
 */
class  TaskService extends Worker
{
    const LOG_ID = "task";

    public $active = true;
    public $maxProcesses = 1;
    public $delay = 72;

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function run()
    {
        date_default_timezone_set('Asia/Yekaterinburg');
        $this->log('run');
        MainFunctions::checkTasks();
        /*        $organisations = Organization::find()->all();
                foreach ($organisations as $organisation) {
                    $this->checkNewTask($organisation);
                    Yii::$app->db->close();
                }*/
    }

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/worker.log';
        parent::init();
    }

    /**
     * 1. читаем таблицу задач
     * 2. упаковываем их в наряд
     * @param $organisation
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function checkNewTask($organisation)
    {
        $return = "";
        date_default_timezone_set('Asia/Yekaterinburg');
        $this->log('check_new_task');
        $tableSchema = Yii::$app->db->schema->getTableSchema('task_template_equipment');
        if ($tableSchema != null) {
            $taskTemplateEquipments = TaskTemplateEquipment::find()
                ->all();
            foreach ($taskTemplateEquipments as $taskTemplateEquipment) {
                $selected_user = $taskTemplateEquipment->getUser();
                $systemUser = Users::find()->where(['uuid' => Users::USER_SERVICE_UUID])->one();
                if ($selected_user)
                    $user = $selected_user;
                else
                    $user = null;
                //$taskTemplateEquipment->formDates();
                $dates = $taskTemplateEquipment->getDates();
                if ($dates) {
                    $count = 0;
                    while ($count < count($dates)) {
                        $start = strtotime($dates[$count]);
                        $date = date('Y-m-d h', $start);
                        $current = date('Y-m-d h', time());
                        $this->log($date. ' = ' .$current);
                        if ($date == $current) {
                            $task = new Task();
                            $task->uuid = MainFunctions::GUID();
                            //$task->equipmentUuid = $task_equipment_stage['equipmentStage']['equipment']->uuid;
                            $task->taskTemplateUuid = $taskTemplateEquipment['taskTemplateUuid'];
                            $task->comment = 'Задача создана автоматически по плану расписанию';
                            $task->workStatusUuid = WorkStatus::NEW;
                            $task->authorUuid = $systemUser['uuid'];
                            $task->equipmentUuid = $taskTemplateEquipment['equipmentUuid'];
                            $task->taskVerdictUuid = TaskVerdict::NOT_DEFINED;
                            $task->taskDate = date('Y-m-d H:i:s');
                            $task->deadlineDate = date('Y-m-d H:i:s', time() + $taskTemplateEquipment['normative'] * 60 * 3600);
                            if (!$task->save()) {
                                $this->log('Ошибка автоматического создания задачи' . json_encode($task->errors));
                                return -1;
                            }
                            $this->log('successfully created order and add task');
                        }
                    }
                }
            }
        }
        return $return;
    }
}
