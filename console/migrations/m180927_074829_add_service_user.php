<?php

use common\models\User;
use common\models\Users;

/**
 * Class m180927_074829_add_service_user
 */
class m180927_074829_add_service_user extends \console\yii2\Migration
{
    const AUTH_KEY = 'K4g2d-bTENTHzzAJp22G1yF6otaUj9EF';
    const USERS_PIN_MD5 = '303f8364456898f50c877766f2f1f5ae';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');

        $this->insert('{{%user}}', [
            'username' => 'sUser',
            'auth_key' => self::AUTH_KEY,
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash(self::AUTH_KEY),
            'email' => 'demonwork8@yandex.ru',
            'status' => '10',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);

        $user = User::find()->where(['username' => 'sUser'])->one();
        if ($user) {
            $this->insert('{{%users}}', [
                'uuid' => Users::USER_SERVICE_UUID,
                'name' => 'sUser',
                'pin' => self::USERS_PIN_MD5,
                'contact' => 'none',
                'user_id' => $user['_id'],
                'createdAt' => $currentTime,
                'changedAt' => $currentTime
            ]);
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%users}}', ['name' => 'sUser']);
        $this->delete('{{%user}}', ['username' => 'sUser']);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180927_074829_add_service_user cannot be reverted.\n";

        return false;
    }
    */
}
