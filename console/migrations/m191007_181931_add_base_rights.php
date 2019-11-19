<?php

use common\models\User;
use yii\db\Migration;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;

/**
 * Class m191002_081245_add
 */
class m191007_181931_add_base_rights extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $am = Yii::$app->authManager;

        $permissions = $am->getPermissions();
        $adminRoleObj = $am->getRole(User::ROLE_ADMIN);
        $operatorRoleObj = $am->getRole(User::ROLE_OPERATOR);
        $dispatchRoleObj = $am->getRole(User::ROLE_DISPATCH);
        $directorRoleObj = $am->getRole(User::ROLE_DIRECTOR);
        foreach ($permissions as $permission) {
            if (strstr($permission->name, 'edit')) {
                $this->addPerm2Role($am, $adminRoleObj, $permission);
                $this->addPerm2Role($am, $operatorRoleObj, $permission);
            } else {
                $this->addPerm2Role($am, $adminRoleObj, $permission);
                $this->addPerm2Role($am, $operatorRoleObj, $permission);
                $this->addPerm2Role($am, $dispatchRoleObj, $permission);
                $this->addPerm2Role($am, $directorRoleObj, $permission);
            }

        }
    }

    /**
     * @param $am ManagerInterface
     * @param $role Item
     * @param $perm Item
     * @throws \yii\base\Exception
     */
    private function addPerm2Role($am, $role, $perm)
    {
        if ($am->canAddChild($role, $perm)) {
            $am->addChild($role, $perm);
        }
    }

    /**
     * {@inheritdoc}
     */
    public
    function safeDown()
    {
        echo "m191002_081245_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191002_081245_add cannot be reverted.\n";

        return false;
    }
    */
}
