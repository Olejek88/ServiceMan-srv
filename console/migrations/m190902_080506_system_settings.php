<?php

use yii\db\Migration;

/**
 * Class m190902_080506_system_settings
 */
class m190902_080506_system_settings extends Migration
{
    const SYS_SETTINGS = '{{%system_settings}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::SYS_SETTINGS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'parameter' => $this->string(45)->notNull(),
            'value' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-parameter',
            self::SYS_SETTINGS,
            'parameter'
        );

        $this->addForeignKey(
            'fk-syssettings-organization-oid',
            self::SYS_SETTINGS,
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
        echo "m190902_080506_system_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190902_080506_system_settings cannot be reverted.\n";

        return false;
    }
    */
}
