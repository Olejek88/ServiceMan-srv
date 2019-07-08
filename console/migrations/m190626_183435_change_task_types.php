<?php

use common\models\TaskTemplate;
use common\models\TaskType;
use yii\db\Migration;

/**
 * Class m190626_183435_change_task_types
 */
class m190626_183435_change_task_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_CURRENT_REPAIR, 'Текущий ремонт');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_PLAN_REPAIR, 'Плановый ремонт');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_CURRENT_CHECK, 'Текущий осмотр');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_NOT_PLANNED_CHECK, 'Внеочередной осмотр');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_SEASON_CHECK, 'Сезонный осмотр');
        //$this->insertIntoType('task_type',TaskType::TASK_TYPE_CONTROL, 'Контроль и проверка');
        //$this->insertIntoType('task_type',TaskType::TASK_TYPE_REPAIR, 'Устранение аварий');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_NOT_PLAN_TO, 'Внеплановое осблуживание');
        //$this->insertIntoType('task_type',TaskType::TASK_TYPE_PLAN_TO, 'Плановое осблуживание');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_MEASURE, 'Снятие показаний');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_POVERKA, 'Поверка');
        //$this->insertIntoType('task_type',TaskType::TASK_TYPE_INSTALL, 'Монтаж');

        $taskTemplates = TaskTemplate::find()->where(['taskTypeUuid' => TaskType::TASK_TYPE_REPAIR])->all();
        foreach ($taskTemplates as $taskTemplate) {
            $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_CURRENT_REPAIR;
            $taskTemplate->save();
        }
        $taskType = TaskType::find()->where(['uuid' => TaskType::TASK_TYPE_REPAIR])->
            orWhere(['uuid' => 'E760D558-CD1C-4674-9D7E-10CCF00CD382'])->one();
        if ($taskType)
            $taskType->delete();

        $taskTemplates = TaskTemplate::find()->where(['taskTypeUuid' => TaskType::TASK_TYPE_OVERHAUL])->all();
        foreach ($taskTemplates as $taskTemplate) {
            $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_PLAN_REPAIR;
            $taskTemplate->save();
        }
        $taskType = TaskType::find()->where(['uuid' => TaskType::TASK_TYPE_OVERHAUL])->one();
        if ($taskType)
            $taskType->delete();

        $taskTemplates = TaskTemplate::find()->where(['taskTypeUuid' => TaskType::TASK_TYPE_REPLACE])->all();
        foreach ($taskTemplates as $taskTemplate) {
            $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_INSTALL;
            $taskTemplate->save();
        }
        $taskType = TaskType::find()->where(['uuid' => TaskType::TASK_TYPE_REPLACE])->one();
        if ($taskType)
            $taskType->delete();

        $taskTemplates = TaskTemplate::find()->where(['taskTypeUuid' => TaskType::TASK_TYPE_UNINSTALL])->all();
        foreach ($taskTemplates as $taskTemplate) {
            $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_INSTALL;
            $taskTemplate->save();
        }
        $taskType = TaskType::find()->where(['uuid' => TaskType::TASK_TYPE_UNINSTALL])->one();
        if ($taskType)
            $taskType->delete();

        $taskTemplates = TaskTemplate::find()->where(['taskTypeUuid' => TaskType::TASK_TYPE_OVERHAUL])->all();
        foreach ($taskTemplates as $taskTemplate) {
            $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_NOT_PLAN_TO;
            $taskTemplate->save();
        }
        $taskType = TaskType::find()->where(['uuid' => TaskType::TASK_TYPE_OVERHAUL])->one();
        if ($taskType)
            $taskType->delete();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190626_183435_change_task_types cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $uuid, $title) {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }
}
