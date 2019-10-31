<?php

use common\models\Settings;
use yii\db\Migration;

/**
 * Class m191031_103105_add_is_ip_setting
 */
class m191031_103105_add_is_ip_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%settings}}', [
            'uuid' => Settings::SETTING_IS_IP,
            'title' => 'api.is74.ru',
            'parameter' => '78.29.1.46',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191031_103105_add_is_ip_setting cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191031_103105_add_is_ip_setting cannot be reverted.\n";

        return false;
    }
    */
}
