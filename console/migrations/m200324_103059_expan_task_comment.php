<?php

use yii\db\Migration;

/**
 * Class m200324_103059_expan_task_comment
 */
class m200324_103059_expan_task_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%task}}', 'comment', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200324_103059_expan_task_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200324_103059_expan_task_comment cannot be reverted.\n";

        return false;
    }
    */
}
