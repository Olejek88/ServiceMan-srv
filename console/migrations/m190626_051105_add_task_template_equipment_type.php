<?php

use yii\db\Migration;

/**
 * Class m190626_051105_add_task_template_equipment_type
 */
class m190626_051105_add_task_template_equipment_type extends Migration
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

        $this->createTable('{{%task_template_equipment_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'taskTemplateUuid' => $this->string(45)->notNull(),
            'equipmentTypeUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentTypeUuid',
            'task_template_equipment_type',
            'equipmentTypeUuid'
        );

        $this->addForeignKey(
            'fk-task_template_equipment_type-equipmentTypeUuid',
            'task_template_equipment_type',
            'equipmentTypeUuid',
            'equipment_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-taskTemplateUuid',
            'task_template_equipment_type',
            'taskTemplateUuid'
        );

        $this->addForeignKey(
            'fk-task_template_equipment_type-taskTemplateUuid',
            'task_template_equipment_type',
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
        echo "m190626_051105_add_task_template_equipment_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190626_051105_add_task_template_equipment_type cannot be reverted.\n";

        return false;
    }
    */
}
