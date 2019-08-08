<?php

namespace common\components;

use common\models\EquipmentStatus;
use common\models\Journal;
use common\models\Operation;
use common\models\OperationTemplate;
use common\models\Task;
use common\models\TaskUser;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\WorkStatus;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;

class MainFunctions
{
    /**
     * Возвращает разницу во времени для операций. Проверяет время на корректность.
     *
     * @param string $beginDate - дата начала
     * @param string $endDate - дата окончания
     * @param integer $limit - предел в секундах
     *
     * @return integer Время выполнения.
     */
    public static function getOperationLength($beginDate, $endDate, $limit)
    {
        // даты должны быть не из прошлого века
        // дата окончания старше даты начала
        // время выполнения не больше лимита
        if (strtotime($endDate) > 10000000 && strtotime($beginDate) > 10000000 && strtotime($endDate) > strtotime($beginDate) && (strtotime($endDate) - strtotime($beginDate)) < $limit) {
            return strtotime($endDate) - strtotime($beginDate);
        } else
            return 0;
    }

    static function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Возвращает  случайный цвет в hex формате.
     *
     * @return string Цвет в hex формате.
     */
    public static function random_color()
    {
        return MainFunctions::random_color_part() . MainFunctions::random_color_part() . MainFunctions::random_color_part();
    }

    /**
     * Logs one or several messages into daemon log file.
     * @param string $filename
     * @param array|string $messages
     */
    public static function log($filename, $messages)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            file_put_contents($filename, date('d.m.Y H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Logs message to journal register in db
     * @param $type
     * @param $title
     * @param string $description сообщение в журнал
     * @return integer код ошибкиы
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function register($type, $title, $description)
    {
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::find()
            ->where(['user_id' => $accountUser['id']])
            ->asArray()
            ->one();
        $journal = new Journal();
        $journal->userUuid = $currentUser['uuid'];
        $journal->description = $description;
        $journal->type = $type;
        $journal->title = $title;
        $journal->date = date('Y-m-d H:i:s');
        if ($journal->save())
            return Errors::OK;
        else {
            return Errors::ERROR_SAVE;
        }
    }

/**
     * return generated UUID
     * @return string generated UUID
     */
    static function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    static function logs($str)
    {
        $handle = fopen("1.txt", "r+");
        fwrite($handle, $str);
        fclose($handle);
    }

    /**
     * Find nearest location of user by coordinates
     * @param Users $user
     * @param boolean $full
     * @return string название локации
     */
    public static function getLocationByUser($user, $full)
    {
        $location = 'не определено';
        /*        $gps = Gpstrack::find()->where(['userUuid' => $user['uuid']])->orderBy('date DESC')->one();
                $objects = Objects::find()->all();
                $max_distance=10;
                foreach ($objects as $object) {
                    $distance=abs(sqrt(($object['latitude']-$gps['latitude'])^2+($object['longitude']-$gps['longitude'])^2));
                    if ($distance<$max_distance) {
                        $max_distance = $distance;
                        $location = $object['title'];
                        if ($full)
                            $location .= ' ['.$gps['latitude'].', '.$gps['longitude'].']';
                    }
                }*/
        return $location;
    }

    /**
     * Sort array by param
     * @param $array
     * @param $cols
     * @return array
     */
    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }

    public static function getColorLabelByStatus($status, $type)
    {
        $label = '<div class="progress"><div class="critical3">' . $status['title'] . '</div></div>';
        if ($type == 'work_status') {
            if ($status["uuid"] == WorkStatus::NEW ||
                $status["uuid"] == WorkStatus::IN_WORK)
                $label = '<div class="progress"><div class="critical5">' . $status['title'] . '</div></div>';
            if ($status["uuid"] == WorkStatus::CANCELED)
                $label = '<div class="progress"><div class="critical2">' . $status['title'] . '</div></div>';
            if ($status["uuid"] == WorkStatus::UN_COMPLETE)
                $label = '<div class="progress"><div class="critical1">' . $status['title'] . '</div></div>';
        }
        if ($type == 'work_status_edit') {
            if ($status["uuid"] == WorkStatus::NEW ||
                $status["uuid"] == WorkStatus::IN_WORK)
                $label = "<span class='badge' style='gray; height: 12px; margin-top: -3px'> </span>&nbsp;". $status['title'];
            else if ($status["uuid"] == WorkStatus::CANCELED)
                $label = "<span class='badge' style='orange; height: 12px; margin-top: -3px'> </span>&nbsp;". $status['title'];
            else
                $label = "<span class='badge' style='green; height: 12px; margin-top: -3px'> </span>&nbsp;". $status['title'];
        }
        if ($type == "task_verdict") {
            if ($status["uuid"] == TaskVerdict::NOT_DEFINED)
                $label = '<div class="critical5">' . $status['title'] . '</div>';
            else if ($status["uuid"] == TaskVerdict::INSPECTED)
                $label = '<div class="critical1">' . $status['title'] . '</div>';
            else
                $label = '<div class="critical2">' . $status['title'] . '</div>';
        }
        if ($type == 'equipment_status') {
            if ($status['uuid'] == EquipmentStatus::NOT_MOUNTED) {
                $label = 'critical1';
            } elseif ($status['uuid'] == EquipmentStatus::NOT_WORK) {
                $label = 'critical2';
            } elseif ($status['uuid'] == EquipmentStatus::UNKNOWN) {
                $label = 'critical4';
            } else {
                $label = 'critical3';
            }
        }
        return $label;
    }

    public static function getAddButton($link)
    {
        return "{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"" . $link . "\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}";
    }

    /**
     * @param $taskTemplate
     * @param $equipmentUuid
     * @param $comment
     * @param $oid
     * @param $userUuid
     * @param $model
     * @return Task|null
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function createTask($taskTemplate, $equipmentUuid, $comment, $oid, $userUuid, $model)
    {
        date_default_timezone_set('Asia/Yekaterinburg');
        $task = new Task();
        $task->uuid = MainFunctions::GUID();
        $task->taskTemplateUuid = $taskTemplate['uuid'];
        $task->oid = $oid;
        $task->equipmentUuid = $equipmentUuid;
        $task->workStatusUuid = WorkStatus::NEW;
        $task->taskVerdictUuid = TaskVerdict::NOT_DEFINED;
        $task->taskDate = date('Y-m-d H:i:s',time());
        if ($taskTemplate['normative'] == 0)
            $task->deadlineDate = date('Y-m-d H:i:s', time() + 1800);
        else
            $task->deadlineDate = date('Y-m-d H:i:s', time() + $taskTemplate['normative'] * 3600);
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
        $task->authorUuid = $currentUser['uuid'];
        $task->comment = $comment;
        if ($model) {
            $task->authorUuid = $model->authorUuid;
            $task->taskDate = $model->taskDate;
            $task->deadlineDate = $model->deadlineDate;
        }
        if (!$task->save()) {
            //MainFunctions::log("request.log", json_encode($task->errors));
            return null;
        } else {
            if ($userUuid) {
                $taskUser = new TaskUser();
                $taskUser->uuid = MainFunctions::GUID();
                $taskUser->taskUuid = $task->uuid;
                $taskUser->userUuid = $userUuid;
                $taskUser->oid = $oid;
                if (!$taskUser->save()) {
                    //MainFunctions::log("request.log", json_encode($taskUser->errors));
                    return null;
                }
            }
            $operationTemplates = OperationTemplate::find()
                ->where(['uuid' => $task['taskTemplateUuid']])
                ->all();
            foreach ($operationTemplates as $operationTemplate) {
                self::createOperation($operationTemplate['operationTemplate']['uuid'], $task['uuid'], $oid);
            }

        }
        //MainFunctions::log("request.log", "create new task " . $task->uuid . ' [' . $taskTemplate['uuid'] . ']');
        return $task;
    }

    private
    static function createOperation($operationTemplateUuid, $taskUuid, $oid)
    {
        $operation = new Operation();
        $operation->uuid = MainFunctions::GUID();
        $operation->operationTemplateUuid = $operationTemplateUuid;
        $operation->taskUuid = $taskUuid;
        $operation->oid = $oid;
        $operation->workStatusUuid = WorkStatus::NEW;
        if (!$operation->save()) {
            MainFunctions::log("request.log", json_encode($operation->errors));
            return null;
        }
        MainFunctions::log("request.log", "create new operation " . $operation->uuid . ' [' . $operationTemplateUuid . ']');
        return $operation;
    }
}

