<?php

use yii\db\Migration;

/**
 * Class m200306_072347_expand_request_comment
 */
class m200306_072347_expand_request_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%request}}', 'comment', $this->text()->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200306_072347_expand_request_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200306_072347_expand_request_comment cannot be reverted.\n";

        return false;
    }
    */
}
