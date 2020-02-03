<?php

use yii\db\Migration;

/**
 * Class m200124_063204_add_comments
 */
class m200124_063204_add_comments extends Migration
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

        $this->createTable('{{%comments}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'entityUuid' => $this->string(45)->notNull(),
            'text' => $this->text(),
            'extId' => $this->string(45)->null(),
            'extParentId' => $this->string(45)->null(),
            'extParentType' => $this->string(45)->null(),
            'rawData' => $this->text(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'integrationClass' => $this->string(128)->null(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('comment_ext_system_uniq', '{{%comments}}', [
            'oid',
            'extId',
            'extParentId',
            'extParentType',
            'integrationClass',
        ], true);
        $this->addForeignKey(
            'fk-comments-organization-oid',
            '{{%comments}}',
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
        echo "m200124_063204_add_comments cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200124_063204_add_comments cannot be reverted.\n";

        return false;
    }
    */
}
