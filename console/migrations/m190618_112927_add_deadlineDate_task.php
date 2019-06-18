<?php

use yii\db\Migration;

/**
 * Class m190618_112927_add_deadlineDate_task
 */
class m190618_112927_add_deadlineDate_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task','deadlineDate', $this->timestamp()->notNull()->defaultValue('2019-01-01'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190618_112927_add_deadlineDate_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190618_112927_add_deadlineDate_task cannot be reverted.\n";

        return false;
    }
    */
}
