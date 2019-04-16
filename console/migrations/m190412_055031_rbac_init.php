<?php

use console\yii2\Migration;

/**
 * Class m190412_055031_rbac_init
 */
class m190412_055031_rbac_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = \Yii::$app->authManager;

        $role = $auth->createRole(common\models\User::ROLE_ADMIN);
        $auth->add($role);
        $perm = $auth->createPermission(common\models\User::PERMISSION_ADMIN);
        $auth->add($perm);
        $auth->addChild($role, $perm);

        $role = $auth->createRole(common\models\User::ROLE_OPERATOR);
        $auth->add($role);
        $perm = $auth->createPermission(common\models\User::PERMISSION_OPERATOR);
        $auth->add($perm);
        $auth->addChild($role, $perm);

        $role = $auth->createRole(common\models\User::ROLE_ANALYST);
        $auth->add($role);
        $perm = $auth->createPermission(common\models\User::PERMISSION_ANALYST);
        $auth->add($perm);
        $auth->addChild($role, $perm);

        $role = $auth->createRole(common\models\User::ROLE_USER);
        $auth->add($role);
        $perm = $auth->createPermission(common\models\User::PERMISSION_USER);
        $auth->add($perm);
        $auth->addChild($role, $perm);
    }


    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m190412_055031_rbac_init cannot be reverted.\n";

        $auth = \Yii::$app->authManager;

        $role = $auth->getRole(common\models\User::ROLE_USER);
        $perm = $auth->getPermission(common\models\User::PERMISSION_USER);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);
        $auth->remove($role);

        $role = $auth->getRole(common\models\User::ROLE_ANALYST);
        $perm = $auth->getPermission(common\models\User::PERMISSION_ANALYST);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);
        $auth->remove($role);

        $role = $auth->getRole(common\models\User::ROLE_OPERATOR);
        $perm = $auth->getPermission(common\models\User::PERMISSION_OPERATOR);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);
        $auth->remove($role);

        $role = $auth->getRole(common\models\User::ROLE_ADMIN);
        $perm = $auth->getPermission(common\models\User::PERMISSION_ADMIN);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);
        $auth->remove($role);

        return true;
    }
}
