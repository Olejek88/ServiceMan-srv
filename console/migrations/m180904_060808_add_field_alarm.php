<?php

/**
 * Class m180904_060808_add_field_alarm
 */
class m180904_060808_add_field_alarm extends \console\yii2\Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('alarm', 'createdAt', $this->timestamp()->notNull());
        $this->addColumn('alarm', 'changedAt', $this->timestamp()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180904_060808_add_field_alarm cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180904_060808_add_field_alarm cannot be reverted.\n";

        return false;
    }
    */
}
