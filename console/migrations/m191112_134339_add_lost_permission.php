<?php

use common\models\IPermission;
use common\models\Organization;
use common\models\User;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;

/**
 * Class m191112_134339_add_lost_permission
 */
class m191112_134339_add_lost_permission extends Migration
{
    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function safeUp()
    {
        Yii::$app->set('db', $this->db);
        $organisations = Organization::find()->all();
        $am = Yii::$app->authManager;
        $models = [
            'common\models\TaskTemplateEquipment',
            'common\models\UserHouse',

        ];

        $adminRoleObj = $am->getRole(User::ROLE_ADMIN);
        $operatorRoleObj = $am->getRole(User::ROLE_OPERATOR);
        $dispatchRoleObj = $am->getRole(User::ROLE_DISPATCH);
        $directorRoleObj = $am->getRole(User::ROLE_DIRECTOR);

        foreach ($organisations as $organisation) {
            foreach ($models as $modelClass) {
                /** @var IPermission $model */
                $model = new $modelClass;
                $permissions = [];
                try {
                    $permissions = $model->getPermissions();
                } catch (Exception $e) {
                }

                foreach ($permissions as $permissionName => $description) {
                    $modelName = explode('\\', $modelClass);
                    $permissionName .= end($modelName) . '-' . $organisation->uuid;
                    $permission = $am->createPermission($permissionName);
                    $permission->description = $description;
                    try {
                        $am->add($permission);

                        if (strstr($permission->name, 'edit')) {
                            $this->addPerm2Role($am, $adminRoleObj, $permission);
                            $this->addPerm2Role($am, $operatorRoleObj, $permission);
                        } else {
                            $this->addPerm2Role($am, $adminRoleObj, $permission);
                            $this->addPerm2Role($am, $operatorRoleObj, $permission);
                            $this->addPerm2Role($am, $dispatchRoleObj, $permission);
                            $this->addPerm2Role($am, $directorRoleObj, $permission);
                        }
                    } catch (Exception $e) {
                    }
                }
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
    public function safeDown()
    {
        echo "m191112_134339_add_lost_permission cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191112_134339_add_lost_permission cannot be reverted.\n";

        return false;
    }
    */
}
