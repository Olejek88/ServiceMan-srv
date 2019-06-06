<?php

use console\yii2\Migration;

/**
 * Class m190515_123153_add_field_to
 */
class m190515_123153_add_field_to_journal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%journal}}', 'type', $this->string()->defaultValue("нет"));
        $this->addColumn('{{%journal}}', 'title', $this->string()->defaultValue("нет"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_123153_add_field_to cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_123153_add_field_to cannot be reverted.\n";

        return false;
    }
    */
}
