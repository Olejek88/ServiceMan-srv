<?php

use yii\db\Migration;

/**
 * Class m191031_105540_fix_request_equipment
 */
class m191031_105540_fix_request_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%request}}', 'equipmentUuid', $this->string(45)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191031_105540_fix_request_equipment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191031_105540_fix_request_equipment cannot be reverted.\n";

        return false;
    }
    */
}
