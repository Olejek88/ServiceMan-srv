<?php

use yii\db\Migration;

/**
 * Class m190528_102659_add_task_template_equipment
 */
class m190528_102659_add_task_template_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%task_template_equipment}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'taskTemplateUuid' => $this->string(45)->notNull(),
            'equipmentUuid' => $this->string(45)->notNull(),
            'period' => $this->string(45),
            'last_date' => $this->timestamp()->notNull(),
            'next_dates' => $this->string(500),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentUuid',
            'task_template_equipment',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-task_template_equipment-equipmentUuid',
            'task_template_equipment',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-taskTemplateUuid',
            'task_template_equipment',
            'taskTemplateUuid'
        );

        $this->addForeignKey(
            'fk-task_template_equipment-taskTemplateUuid',
            'task_template_equipment',
            'taskTemplateUuid',
            'task_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%task_template_equipment}}');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190528_102659_add_task_template_equipment cannot be reverted.\n";

        return false;
    }
    */
}
