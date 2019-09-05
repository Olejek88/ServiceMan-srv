<?php

use yii\db\Migration;

/**
 * Class m190904_112233_add_uuid_to_journal
 */
class m190904_112233_add_uuid_to_journal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%journal}}', 'referenceUuid', $this->string(45));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190904_112233_add_uuid_to_journal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190904_112233_add_uuid_to_journal cannot be reverted.\n";

        return false;
    }
    */
}
