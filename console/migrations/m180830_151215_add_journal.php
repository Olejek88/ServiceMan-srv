<?php

/**
 * Class m180830_151215_add_journal
 */
class m180830_151215_add_journal extends \console\yii2\Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%journal}}', [
          '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
          0 => 'PRIMARY KEY (`_id`)',
          'userUuid' => 'VARCHAR(50) NOT NULL',
          'description' => 'TEXT NOT NULL',
          'date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP '
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180830_151215_add_journal cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180830_151215_add_journal cannot be reverted.\n";

        return false;
    }
    */
}
