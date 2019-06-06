<?php

use yii\db\Migration;

/**
 * Class m190606_131059_fix_users2user_link
 */
class m190606_131059_fix_users2user_link extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%users}}', 'user_id', $this->integer()->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190606_131059_fix_users2user_link cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190606_131059_fix_users2user_link cannot be reverted.\n";

        return false;
    }
    */
}
