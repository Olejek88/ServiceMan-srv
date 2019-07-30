<?php

use yii\db\Migration;

/**
 * Class m190723_130301_change_contragent
 */
class m190723_130301_change_contragent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('contragent','phone', $this->string(45)->notNull()->defaultValue("не указан"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190723_130301_change_contragent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190723_130301_change_contragent cannot be reverted.\n";

        return false;
    }
    */
}
