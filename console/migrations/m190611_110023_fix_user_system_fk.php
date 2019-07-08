<?php

use yii\db\Migration;

/**
 * Class m190611_110023_fix_user_system_fk
 */
class m190611_110023_fix_user_system_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-user_system-equipmentSystemUuid','user_system');
        $this->addForeignKey(
            'fk-user_system-equipmentSystemUuid',
            'user_system',
            'equipmentSystemUuid',
            'equipment_system',
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
        echo "m190611_110023_fix_user_system_fk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190611_110023_fix_user_system_fk cannot be reverted.\n";

        return false;
    }
    */
}
