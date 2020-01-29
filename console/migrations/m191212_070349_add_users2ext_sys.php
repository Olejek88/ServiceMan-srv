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

        $this->createTable('{{%ext_system_user}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'extId' => $this->string(45)->notNull(),
            'fullName' => $this->string(64)->notNull(),
            'rawData' => $this->text(),
            'integrationClass' => $this->string(128)->null(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('ext_system_user-extId', '{{%ext_system_user}}', ['oid', 'extId', 'integrationClass'], true);
        $this->addForeignKey(
            'fk-ext_system_user-organization-oid',
            '{{%ext_system_user}}',
            'oid',
            '{{%organization}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        // исправляем поле для связи контрагента с пользователем из внешней системы
        $this->dropColumn('{{%contragent}}', 'extId');
        $this->addColumn('{{%contragent}}', 'extSystemUserUuid', $this->string('45')->null());
        $this->addForeignKey(
            'fk-contraget-extSystemUserUuid-ext_system_user-uuid',
            '{{%contragent}}',
            'extSystemUserUuid',
            '{{%ext_system_user}}',
            'uuid'
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
