<?php

use yii\db\Migration;

/**
 * Class m191212_070349_add_users2ext_sys
 */
class m191212_070349_add_users2ext_sys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users_ext_system}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'usersUuid' => $this->string(45)->notNull(),
            'extId' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('users_ext_system-extId', '{{%users_ext_system}}', ['oid', 'usersUuid', 'extId'], true);
        $this->addForeignKey(
            'users_ext_system-usersUuid-users_uuid',
            '{{%users_ext_system}}',
            ['usersUuid'],
            '{{%users}}',
            ['uuid']
        );
        $this->addForeignKey(
            'fk-users_ext_system-organization-oid',
            '{{%users_ext_system}}',
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
        echo "m191212_070349_add_users2ext_sys cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_070349_add_users2ext_sys cannot be reverted.\n";

        return false;
    }
    */
}
