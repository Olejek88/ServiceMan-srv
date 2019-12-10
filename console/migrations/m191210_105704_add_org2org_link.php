<?php

use yii\db\Migration;

/**
 * Class m191210_105704_add_org2org_link
 */
class m191210_105704_add_org2org_link extends Migration
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

        $this->createTable('{{%organization_sub}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'masterUuid' => $this->string(45)->notNull(),
            'subUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('org_sub_master-sub', '{{%organization_sub}}', ['masterUuid', 'subUuid'], true);
        $this->addForeignKey('org_sub_master-org', '{{%organization_sub}}', ['masterUuid'], '{{%organization}}', ['uuid']);
        $this->addForeignKey('org_sub_sub-org', '{{%organization_sub}}', ['subUuid'], '{{%organization}}', ['uuid']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191210_105704_add_org2org_link cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191210_105704_add_org2org_link cannot be reverted.\n";

        return false;
    }
    */
}
