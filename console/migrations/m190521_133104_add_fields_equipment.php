<?php

use yii\db\Migration;

/**
 * Class m190521_133104_add_fields_equipment
 */
class m190521_133104_add_fields_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%equipment}}', 'period', $this->integer()->defaultValue(365));
        $this->addColumn('{{%equipment}}', 'replaceDate', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190521_133104_add_fields_equipment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190521_133104_add_fields_equipment cannot be reverted.\n";

        return false;
    }
    */
}
