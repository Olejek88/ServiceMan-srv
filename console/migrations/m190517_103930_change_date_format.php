<?php

use yii\db\Migration;

/**
 * Class m190517_103930_change_date_format
 */
class m190517_103930_change_date_format extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->dropColumn('{{%task}}', 'date');
        //$this->dropColumn('{{%task}}', 'startDate');
        //$this->dropColumn('{{%task}}', 'endDate');

        $this->addColumn('{{%task}}', 'startDate', $this->dateTime()->defaultExpression("NULL"));
        $this->addColumn('{{%task}}', 'endDate', $this->dateTime()->defaultExpression("NULL"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190517_103930_change_date_format cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_103930_change_date_format cannot be reverted.\n";

        return false;
    }
    */
}
