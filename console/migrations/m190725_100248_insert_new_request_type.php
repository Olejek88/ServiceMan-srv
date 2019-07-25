<?php

use common\components\MainFunctions;
use common\models\Organization;
use common\models\TaskType;
use yii\db\Migration;

/**
 * Class m190725_100248_insert_new_request_type
 */
class m190725_100248_insert_new_request_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertIntoRequestType('Внеочередной осмотр при форс-мажорных обстоятельствах',
            24, TaskType::TASK_TYPE_NOT_PLANNED_CHECK);
        $this->insertIntoRequestType('Устранение протечки кровли',
            24, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Устранение повреждения системы организованного водоотвода',
            120, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Устранение утраты связи отдельных кирпичей с кладкой наружных стен, угрожающей их выпадением',
            24, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Устранение повреждения окон подъезда в летний период',
            72, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Устранение повреждения окон подъезда в зимний период',
            24, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Устранение повреждения заполнения входных дверей',
            24, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Отслоение штукатурки потолка или верхней части стены, угрожающее ее обрушению',
            120, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Нарушение связи наружной облицовки на фасадах со стенами',
            1, TaskType::TASK_TYPE_REPAIR);
        $this->insertIntoRequestType('Устранение неисправности лифта',
            24, TaskType::TASK_TYPE_REPAIR);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_100248_insert_new_request_type cannot be reverted.\n";

        return false;
    }

    private function insertIntoRequestType($title, $normative, $taskTypeUuid) {
        $currentTime = date('Y-m-d\TH:i:s');
        $uuid = MainFunctions::GUID();
        $this->insert('task_template', [
            'uuid' => $uuid,
            'title' => $title,
            'description' => $title,
            'normative' => $normative,
            'oid' => Organization::ORG_SERVICE_UUID,
            'taskTypeUuid' => $taskTypeUuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);

        $this->insert('request_type', [
            'uuid' => MainFunctions::GUID(),
            'title' => $title,
            'taskTemplateUuid' => $uuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_100248_insert_new_request_type cannot be reverted.\n";

        return false;
    }
    */
}
