<?php

use yii\db\Migration;

/**
 * Class m190627_064258_add_additional_references
 */
class m190627_064258_drop_unique_oid_user_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->truncateTable('{{%user_system}}');
        //$this->dropForeignKey('fk-user_system-organization-oid','user_system');
        $this->alterColumn('user_system','oid', $this->string(45)->notNull());
        $this->addForeignKey(
            'fk-user_system-organization-oid',
            '{{%user_system}}',
            'oid',
            '{{%organization}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190627_064258_add_additional_references cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_064258_add_additional_references cannot be reverted.\n";

        return false;
    }
    */
}
