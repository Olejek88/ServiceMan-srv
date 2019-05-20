<?php

use yii\db\Migration;

/**
 * Class m190520_121604_fix_key
 */
class m190520_121604_fix_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-task_user-taskUuid','task_user');
        $this->addForeignKey(
            'fk-task_user-taskUuid',
            'task_user',
            'taskUuid',
            'task',
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
        echo "m190520_121604_fix_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_121604_fix_key cannot be reverted.\n";

        return false;
    }
    */
}
