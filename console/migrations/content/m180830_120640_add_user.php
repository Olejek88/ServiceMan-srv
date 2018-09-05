<?php

use yii\db\Migration;

/**
 * Class m180830_120640_add_user
 */
class m180830_120640_add_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $currentTime = date('Y-m-d\TH:i:s');

        $this->insert('{{%user}}', [
            '_id' => '1',
            'username' => 'dev',
            'auth_key' => 'f1elprxfre3ri79clcY2VcaBdPqhPLZQ',
            'password_hash' => '$2y$13$nGZaF9DU5t/v63X./MM3Gu/eg0HsXBRtnBZ7adA3spSbJUKtLIEbC',
            'email' => 'shtrmvk@gmail.com',
            'status' => '10',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180830_120640_add_user cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180830_120640_add_user cannot be reverted.\n";

        return false;
    }
    */
}
