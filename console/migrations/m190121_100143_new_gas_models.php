<?php

use yii\db\Migration;

/**
 * Class m190121_100143_new_gas_models
 */
class m190121_100143_new_gas_models extends Migration
{
    const OPERATION = '{{%operation}}';
    const OPERATION_TEMPLATE = '{{%operation_template}}';
    const TASK = '{{%task}}';
    const WORK_STATUS = '{{%work_status}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $currentTime = date('Y-m-d\TH:i:s');

        $this->createTable(self::OPERATION, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'taskUuid' => $this->string()->notNull(),
            'workStatusUuid' => $this->string()->notNull(),
            'operationTemplateUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ],$tableOptions);

        $this->createTable(self::OPERATION_TEMPLATE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'description' => $this->string()->defaultValue(''),
            'normative' => $this->integer()->defaultValue(0),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ],$tableOptions);

        $this->createTable(self::WORK_STATUS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ],$tableOptions);

        $this->createTable(self::TASK, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'comment' => $this->string()->defaultValue(''),
            'flatUuid' => $this->string(),
            'equipmentUuid' => $this->string(),
            'workStatusUuid' => $this->string()->notNull(),
            'startDate' => $this->dateTime()->notNull(),
            'endDate' => $this->dateTime()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ],$tableOptions);

        $this->createIndex(
            'idx-operation-uuid',
            'operation',
            'uuid'
        );
        $this->createIndex(
            'idx-task_status-uuid',
            'operation',
            'uuid'
        );
        $this->createIndex(
            'idx-operation_template-uuid',
            'operation',
            'uuid'
        );
        $this->createIndex(
            'idx-operation-taskSUuid',
            'operation',
            'taskUuid'
        );

        $this->addForeignKey(
            'fk-operation-operationTemplateUuid',
            'operation',
            'operationTemplateUuid',
            'operation_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-operation-workStatusUuid',
            'operation',
            'workStatusUuid',
            'work_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        // статусы задач и операций
        $this->insertIntoType('work_status', 1, '1E9B4D73-044C-471B-A08D-26F32EBB22B0', 'Новая', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 2, '31179027-8416-47E4-832F-2A94D7804A4F', 'В работе', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 3, 'F1576F3E-ACB6-4EEB-B8AF-E34E4D345CE9', 'Выполнена', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 4, 'EFDE80D2-D00E-413B-B430-0A011056C6EA', 'Не выполнена', $currentTime, $currentTime);
        $this->insertIntoType('work_status', 5, 'C2FA4A7B-0D7C-4407-A449-78FA70A11D47', 'Отменена', $currentTime, $currentTime);

        $this->createIndex(
            'idx-task-workStatusUuid',
            'task',
            'workStatusUuid'
        );
        $this->addForeignKey(
            'fk-task-workStatusUuid',
            'task',
            'workStatusUuid',
            'work_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

/*        $this->addForeignKey(
            'fk-task_stage-equipmentUuid',
            'task_stage',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );*/
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190121_100143_new_gas_models cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $id, $uuid, $title, $createdAt, $changedAt)
    {
        $this->insert($table, [
            '_id' => $id,
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190121_100143_new_gas_models cannot be reverted.\n";

        return false;
    }
    */
}
