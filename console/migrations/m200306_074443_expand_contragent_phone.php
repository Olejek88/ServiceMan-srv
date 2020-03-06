<?php

use yii\db\Migration;

/**
 * Class m200306_074443_expand_contragent_phone
 */
class m200306_074443_expand_contragent_phone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%contragent}}', 'phone', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200306_074443_expand_contragent_phone cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200306_074443_expand_contragent_phone cannot be reverted.\n";

        return false;
    }
    */
}
