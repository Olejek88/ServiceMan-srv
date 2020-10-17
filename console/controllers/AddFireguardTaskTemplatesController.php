<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\components\Tag;
use common\models\Equipment;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\House;
use common\models\Objects;
use common\models\ObjectStatus;
use common\models\ObjectType;
use common\models\Organization;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipmentType;
use common\models\TaskType;
use Exception;
use Throwable;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class AddFireguardTaskTemplatesController extends Controller
{
    public $defaultAction = 'list-org';

    public $uuid = null;

    /**
     * @param $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'uuid';
        return $options;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function actionAdd()
    {
        if (empty($this->uuid)) {
            $this->stdout('Укажите uuid организации!' . PHP_EOL, Console::FG_RED);
            self::actionListOrg();
            exit(1);
        }

        self::createTemplates();

        $houses = House::findAll(['oid' => $this->uuid, 'deleted' => false]);
        foreach ($houses as $house) {
            self::addToHouse($house->uuid);
        }
    }

    /**
     * @param $houseUuid
     * @throws Exception
     */
    public function addToHouse($houseUuid)
    {
        $object = Objects::findOne([
            'objectTypeUuid' => ObjectType::OBJECT_TYPE_GENERAL,
            'title' => 'Пожарная система',
            'houseUuid' => $houseUuid,
            'deleted' => false,
            'oid' => $this->uuid,
        ]);
        if ($object == null) {
            $object = new Objects();
            $object->uuid = MainFunctions::GUID();
            $object->oid = $this->uuid;
            $object->gis_id = null;
            $object->title = 'Пожарная система';
            $object->objectStatusUuid = ObjectStatus::OBJECT_STATUS_OK;
            $object->houseUuid = $houseUuid;
            $object->objectTypeUuid = ObjectType::OBJECT_TYPE_GENERAL;
            $object->deleted = false;
            $object->square = 0;
            if (!$object->save()) {
                $message = '';
                foreach ($object->errors as $error) {
                    $message .= $error[0] . PHP_EOL;
                }

                throw new Exception('Не удалось сохранить общий объект ' . $message);
            }
        }

        self::addEquipment('Пожарный ящик', $object->uuid, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        self::addEquipment('Пожарная кнопка', $object->uuid, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        self::addEquipment('Пожарная сигнализация', $object->uuid, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        self::addEquipment('Пожарный датчик', $object->uuid, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
    }

    /**
     * @throws Exception
     */
    public function createTemplates()
    {
        //
        $check = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where([
                'equipmentTypeUuid' => [
                    EquipmentType::EQUIPMENT_FIREGUARD_BOX,
                    EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
                    EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
                    EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
                ],
                'task_template.title' => 'Текущий ремонт',
            ])->asArray()
            ->all();
        $testList = ArrayHelper::map($check, '_id', 'equipmentTypeUuid');

        if (count($check) == 0) {
            $tt = self::addTaskTemplate('Текущий ремонт', 'Текущий ремонт',
                TaskType::TASK_TYPE_CURRENT_REPAIR);
            $tt = $tt->uuid;
        } else {
            $tt = $check[0]['taskTemplateUuid'];
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BOX, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BUTTON, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_ALARM, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_SENSOR, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
        }

        //
        $check = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where([
                'equipmentTypeUuid' => [
                    EquipmentType::EQUIPMENT_FIREGUARD_BOX,
                    EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
                    EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
                    EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
                ],
                'task_template.title' => 'Аварийное обслуживание',
            ])->asArray()
            ->all();
        $testList = ArrayHelper::map($check, '_id', 'equipmentTypeUuid');

        if (count($check) == 0) {
            $tt = self::addTaskTemplate('Аварийное обслуживание', 'Аварийное обслуживание',
                TaskType::TASK_TYPE_REPAIR);
            $tt = $tt->uuid;
        } else {
            $tt = $check[0]['taskTemplateUuid'];
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BOX, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BUTTON, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_ALARM, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_SENSOR, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
        }

        //
        $check = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where([
                'equipmentTypeUuid' => [
                    EquipmentType::EQUIPMENT_FIREGUARD_BOX,
                    EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
                    EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
                    EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
                ],
                'task_template.title' => 'Текущая замена',
            ])->asArray()
            ->all();
        $testList = ArrayHelper::map($check, '_id', 'equipmentTypeUuid');

        if (count($check) == 0) {
            $tt = self::addTaskTemplate('Текущая замена', 'Текущая замена',
                TaskType::TASK_TYPE_REPLACE);
            $tt = $tt->uuid;
        } else {
            $tt = $check[0]['taskTemplateUuid'];
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BOX, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BUTTON, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_ALARM, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_SENSOR, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
        }

        //
        $check = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where([
                'equipmentTypeUuid' => [
                    EquipmentType::EQUIPMENT_FIREGUARD_BOX,
                    EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
                    EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
                    EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
                ],
                'task_template.title' => 'Замена при аварийной ситуации',
            ])->asArray()
            ->all();
        $testList = ArrayHelper::map($check, '_id', 'equipmentTypeUuid');

        if (count($check) == 0) {
            $tt = self::addTaskTemplate('Замена при аварийной ситуации', 'Замена при аварийной ситуации',
                TaskType::TASK_TYPE_REPAIR);
            $tt = $tt->uuid;
        } else {
            $tt = $check[0]['taskTemplateUuid'];
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BOX, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BUTTON, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_ALARM, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_SENSOR, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
        }

        //
        $check = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where([
                'equipmentTypeUuid' => [
                    EquipmentType::EQUIPMENT_FIREGUARD_BOX,
                    EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
                    EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
                    EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
                ],
                'task_template.title' => 'Текущее обслуживание',
            ])->asArray()
            ->all();
        $testList = ArrayHelper::map($check, '_id', 'equipmentTypeUuid');

        if (count($check) == 0) {
            $tt = self::addTaskTemplate('Текущее обслуживание', 'Текущее обслуживание',
                TaskType::TASK_TYPE_NOT_PLAN_TO);
            $tt = $tt->uuid;
        } else {
            $tt = $check[0]['taskTemplateUuid'];
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BOX, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BUTTON, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_ALARM, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_SENSOR, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
        }

        //
        $check = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where([
                'equipmentTypeUuid' => [
                    EquipmentType::EQUIPMENT_FIREGUARD_BOX,
                    EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
                    EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
                    EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
                ],
                'task_template.title' => 'Плановое обслуживание',
            ])->asArray()
            ->all();
        $testList = ArrayHelper::map($check, '_id', 'equipmentTypeUuid');

        if (count($check) == 0) {
            $tt = self::addTaskTemplate('Плановое обслуживание', 'Плановое обслуживание',
                TaskType::TASK_TYPE_PLAN_TO);
            $tt = $tt->uuid;
        } else {
            $tt = $check[0]['taskTemplateUuid'];
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BOX, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BOX);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_BUTTON, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_BUTTON);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_ALARM, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_ALARM);
        }

        if (!in_array(EquipmentType::EQUIPMENT_FIREGUARD_SENSOR, $testList)) {
            self::addTaskTemplateEquipmentType($tt, EquipmentType::EQUIPMENT_FIREGUARD_SENSOR);
        }

    }

    /**
     * @throws Throwable
     */
    public function actionListOrg()
    {
        $this->stdout("./yii add-fireguard-task-templates/add --uuid=UUID" . PHP_EOL, Console::FG_GREEN);
        $this->stdout("Organization list\n", Console::FG_GREEN);
        $orgs = Organization::find()->all();
        foreach ($orgs as $org) {
            $this->stdout($org->title . ": uuid=" . $org->uuid . PHP_EOL, Console::FG_GREEN);
        }
    }

    /**
     * @param $title
     * @param $decr
     * @param $taskType
     * @param int $normative
     * @return TaskTemplate
     * @throws Exception
     */
    private function addTaskTemplate($title, $decr, $taskType, $normative = 1)
    {
        $item = new TaskTemplate();
        $item->uuid = MainFunctions::GUID();
        $item->title = $title;
        $item->oid = $this->uuid;
        $item->description = $decr;
        $item->taskTypeUuid = $taskType;
        $item->normative = $normative;
        if (!$item->save()) {
            $message = '';
            foreach ($item->errors as $error) {
                $message .= $error[0] . PHP_EOL;
            }

            throw new Exception('Не удалось сохранить шаблон задачи "' . $title . '". ' . $message);
        }

        return $item;
    }

    /**
     * @param $taskTemplate
     * @param $equipmentType
     * @return TaskTemplateEquipmentType
     * @throws Exception
     */
    private function addTaskTemplateEquipmentType($taskTemplate, $equipmentType)
    {
        $item = new TaskTemplateEquipmentType();
        $item->uuid = MainFunctions::GUID();
        $item->taskTemplateUuid = $taskTemplate;
        $item->equipmentTypeUuid = $equipmentType;
        $item->oid = $this->uuid;
        if (!$item->save()) {
            $message = '';
            foreach ($item->errors as $error) {
                $message .= $error[0] . PHP_EOL;
            }
            $info = 'taskTemplateUuid=' . $taskTemplate . '. equipmentTypeUuid=' . $equipmentType;
            throw new Exception('Не удалось сохранить связь шаблона задачи с типом оборудования "' . $info . '". ' . $message);
        }

        return $item;
    }

    /**
     * @param $title
     * @param $object
     * @param $equipmentType
     * @return Equipment
     * @throws Exception
     */
    private function addEquipment($title, $object, $equipmentType)
    {
        $item = new Equipment();
        $item->uuid = MainFunctions::GUID();
        $item->oid = $this->uuid;
        $item->title = $title;
        $item->objectUuid = $object;
        $item->equipmentTypeUuid = $equipmentType;
        $item->equipmentStatusUuid = EquipmentStatus::WORK;
        $item->tag = Tag::TAG_TYPE_GRAPHIC_CODE . ':' . $item->uuid;
        $item->serial = '-';
        $item->testDate = date('Y-01-01');
        $item->deleted = 0;
        $item->period = 0;
        $item->replaceDate = date('Y-01-01', strtotime('+1 year'));
        $item->inputDate = date('Y-m-d H:i:s');

        if (!$item->save()) {
            $message = '';
            foreach ($item->errors as $error) {
                $message .= $error[0] . PHP_EOL;
            }

            throw new Exception('Не удалось сохранить оборудование "' . $title . '". ' . $message);
        }

        return $item;
    }
}