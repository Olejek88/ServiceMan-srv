<?php

use yii\db\Migration;

/**
 * Class m190523_055615_add_receipt_table
 */
class m190523_055615_add_receipt_table extends Migration
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

        $this->createTable('{{%receipt}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'userUuid' => $this->string(45)->notNull(),
            'contragentUuid' => $this->string(45)->notNull(),
            'requestUuid' => $this->string(45),
            'description' => $this->string()->notNull(),
            'result' => $this->string()->notNull(),
            'closed' => $this->boolean(),
            'date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-requestUuid',
            'receipt',
            'requestUuid'
        );

        $this->addForeignKey(
            'fk-receipt-requestUuid',
            'receipt',
            'requestUuid',
            'request',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'receipt',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-receipt-userUuid',
            'receipt',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-contragentUuid',
            'receipt',
            'contragentUuid'
        );

        $this->addForeignKey(
            'fk-receipt-contragentUuid',
            'receipt',
            'contragentUuid',
            'contragent',
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
        echo "m190523_055615_add_receipt_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190523_055615_add_receipt_table cannot be reverted.\n";

        return false;
    }
    */
}
