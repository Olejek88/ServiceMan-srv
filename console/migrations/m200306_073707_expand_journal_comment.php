<?php

use yii\db\Migration;

/**
 * Class m200306_073707_expand_journal_comment
 */
class m200306_073707_expand_journal_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%journal}}', 'description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200306_073707_expand_journal_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200306_073707_expand_journal_comment cannot be reverted.\n";

        return false;
    }
    */
}
