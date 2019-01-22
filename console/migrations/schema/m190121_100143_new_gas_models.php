<?php

use yii\db\Migration;

/**
 * Class m190121_100143_new_gas_models
 */
class m190121_100143_new_gas_models extends Migration
{
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

        $createdAt = 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP';
        $changedAt = 'TIMESTAMP NOT NULL DEFAULT "0000-00-00 00:00:00"';

        $this->createTable('{{%operation}}', [
            '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            0 => 'PRIMARY KEY (`_id`)',
            'uuid' => 'VARCHAR(50) NOT NULL',
            'taskUuid' => 'VARCHAR(50) NOT NULL',
            'taskStatusUuid' => 'VARCHAR(50) NOT NULL',
            'operationTemplateUuid' => 'VARCHAR(50) NOT NULL',
            'startDate' => 'TIMESTAMP NULL',
            'endDate' => 'TIMESTAMP NULL',
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ], $tableOptions);

        $this->createTable('{{%task_status}}', [
            '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            0 => 'PRIMARY KEY (`_id`)',
            'uuid' => 'VARCHAR(50) NOT NULL',
            'title' => 'VARCHAR(200) NOT NULL',
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ], $tableOptions);

        $this->createTable('{{%operation_template}}', [
            '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            0 => 'PRIMARY KEY (`_id`)',
            'uuid' => 'VARCHAR(50) NOT NULL',
            'title' => 'VARCHAR(200) NOT NULL',
            'description' => 'TEXT NOT NULL',
            'normative' => 'INT(10) UNSIGNED NOT NULL',
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ], $tableOptions);

        $this->createTable('{{%task}}', [
            '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            0 => 'PRIMARY KEY (`_id`)',
            'uuid' => 'VARCHAR(50) NOT NULL',
            'comment' => 'TEXT NOT NULL',
            'flatUuid' => 'VARCHAR(50) NOT NULL',
            'taskStatusUuid' => 'VARCHAR(45) NOT NULL',
            'startDate' => 'TIMESTAMP NULL',
            'endDate' => 'TIMESTAMP NULL',
            'createdAt' => $createdAt,
            'changedAt' => $changedAt,
        ], $tableOptions);

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
            'fk-operation-taskStatusUuid',
            'operation',
            'taskStatusUuid',
            'task_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        // статусы задач и операций
        $this->insertIntoType('task_status', 1, '1E9B4D73-044C-471B-A08D-26F32EBB22B0', 'Новая', $createdAt, $changedAt);
        $this->insertIntoType('task_status', 2, '31179027-8416-47E4-832F-2A94D7804A4F', 'В работе', $createdAt, $changedAt);
        $this->insertIntoType('task_status', 3, 'F1576F3E-ACB6-4EEB-B8AF-E34E4D345CE9', 'Выполнена', $createdAt, $changedAt);
        $this->insertIntoType('task_status', 4, 'EFDE80D2-D00E-413B-B430-0A011056C6EA', 'Не выполнена', $createdAt, $changedAt);
        $this->insertIntoType('task_status', 5, 'C2FA4A7B-0D7C-4407-A449-78FA70A11D47', 'Отменена', $createdAt, $changedAt);

        $this->createIndex(
            'idx-task-taskStatusUuid',
            'task',
            'taskStatusUuid'
        );
        $this->addForeignKey(
            'fk-task-taskStatusUuid',
            'task',
            'taskStatusUuid',
            'task_status',
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
