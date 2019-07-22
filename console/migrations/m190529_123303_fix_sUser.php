<?php

use yii\db\Migration;
use common\models\Users;
use common\models\User;
use yii\helpers\Console;

/**
 * Class m190529_123303_fix_sUser
 */
class m190529_123303_fix_sUser extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*        $users = Users::findOne(['name' => 'sUser']);
        if ($users != null) {
            $user = User::findOne(['username' => 'sUser']);
            if ($user != null) {
                $usersTest = Users::findOne(['user_id' => $user->_id]);
                if ($usersTest == null) {
                    $users->user_id = $user->_id;
                    if ($users->save()) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    $msg = Console::ansiFormat('Record from {{%users}} with name "sUser" must be linked with record from {{%user}} with username "sUser"', [Console::FG_RED]);
                    echo $msg . PHP_EOL;
                    $msg = Console::ansiFormat('But record _id=' . $usersTest->_id . ' in {{%users}} exists.', [Console::FG_RED]);
                    echo $msg . PHP_EOL;
                    return false;
                }
            }
        }

        $msg = Console::ansiFormat('Record with name/username "sUser" must be exists in {{%users}} and {{%user}}', [Console::FG_RED]);
        echo $msg . PHP_EOL;*/
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190529_123303_fix_sUser cannot be reverted.\n";

        return true;
    }
}
