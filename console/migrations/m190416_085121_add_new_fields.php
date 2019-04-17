<?php

use yii\db\Migration;

/**
 * Class m190416_085121_add_new_fields
 */
class m190416_085121_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%task}}', 'taskDate', $this->timestamp()->notNull()->defaultExpression('2019-01-01'));
        $this->addColumn('{{%task}}', 'taskTemplateUuid', $this->string(45)->notNull());
        $this->createIndex(
            'idx-taskTemplateUuid',
            'task',
            'taskTemplateUuid'
        );

        $this->addForeignKey(
            'fk-task-taskTemplateUuid',
            'task',
            'taskTemplateUuid',
            'task_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        $this->addColumn('{{%equipment_type}}', 'equipmentSystemUuid', $this->string(45)->notNull());
        $this->createIndex(
            'idx-equipmentSystemUuid',
            'equipment_type',
            'equipmentSystemUuid'
        );

        $this->addForeignKey(
            'fk-equipment_type-equipmentSystemUuid',
            'equipment_type',
            'equipmentSystemUuid',
            'equipment_system',
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
        echo "m190416_085121_add_new_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190416_085121_add_new_fields cannot be reverted.\n";

        return false;
    }
    */
}
