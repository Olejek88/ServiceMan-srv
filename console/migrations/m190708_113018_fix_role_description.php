<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m190708_113018_fix_role_description
 */
class m190708_113018_fix_role_description extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $am = Yii::$app->getAuthManager();

        $role = $am->getRole(User::ROLE_ADMIN);
        $role->description = 'Администратор';
        $am->update(User::ROLE_ADMIN, $role);
        $role = $am->getRole(User::ROLE_OPERATOR);
        $role->description = 'Оператор';
        $am->update(User::ROLE_OPERATOR, $role);
//        $role = $am->getRole(User::ROLE_ANALYST);
//        $role->description = 'Аналитик';
//        $am->update(User::ROLE_ANALYST, $role);
//        $role = $am->getRole(User::ROLE_USER);
//        $role->description = 'Пользователь';
//        $am->update(User::ROLE_USER, $role);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190708_113018_fix_role_description cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190708_113018_fix_role_description cannot be reverted.\n";

        return false;
    }
    */
}
