<?php

use yii\db\Migration;

/**
 * Class m190715_084132_add_user_contragent
 */
class m190715_084132_add_user_contragent extends Migration
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
        $this->createTable('{{%user_contragent}}', [
            '_id' => $this->primaryKey(),
            'oid' => $this->string(45)->notNull(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'userUuid' => $this->string(45)->notNull(),
            'contragentUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_084132_add_user_contragent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_084132_add_user_contragent cannot be reverted.\n";

        return false;
    }
    */
}
