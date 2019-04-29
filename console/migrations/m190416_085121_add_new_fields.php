<?php

use yii\db\Migration;

/**
 * Class m190416_085121_add_new_fields
 */
class m190416_085121_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%task}}', 'taskDate', $this->timestamp()->notNull()->defaultValue('2019-01-01'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190416_085121_add_new_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190416_085121_add_new_fields cannot be reverted.\n";

        return false;
    }
    */
}
