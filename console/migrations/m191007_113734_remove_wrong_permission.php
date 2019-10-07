<?php

use yii\db\Migration;

/**
 * Class m191007_113734_remove_wrong_permission
 */
class m191007_113734_remove_wrong_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $am = Yii::$app->authManager;
        $am->removeAllPermissions();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_113734_remove_wrong_permission cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_113734_remove_wrong_permission cannot be reverted.\n";

        return false;
    }
    */
}
