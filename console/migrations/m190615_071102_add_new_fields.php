<?php

use yii\db\Migration;

/**
 * Class m190615_071102_add_new_fields
 */
class m190615_071102_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('object', 'square', $this->double()->defaultValue(0));
        $this->addColumn('{{%equipment}}', 'inputDate', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190615_071102_add_new_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190615_071102_add_new_fields cannot be reverted.\n";

        return false;
    }
    */
}
