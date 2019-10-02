<?php

use yii\db\Migration;
use yii\rbac\Role;

/**
 * Class m190926_114204_change_permissions
 */
class m190926_114204_change_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $am = Yii::$app->authManager;
        $forDelete = [
            'permissionAdmin',
            'permissionOperator',
            'permissionAnalyst',
            'permissionUser',
        ];
        foreach ($forDelete as $permission) {
            $item = $am->getPermission($permission);
            if ($item != null) {
                $am->remove($item);
            }
        }

        $forDelete = [
            // подсмотрел в базе, что кроме администраторов и операторов пока ни кого нет, удаляем без проверки
            'analyst',
            'user',
        ];
        foreach ($forDelete as $roleName) {
            $item = $am->getRole($roleName);
            if ($item != null) {
                $am->remove($item);
            }
        }

        // создаём небоходмые роли
        $forCreate = [
            'dispatch' => 'Диспетчер',
            'director' => 'Директор',
        ];
        foreach ($forCreate as $name => $description) {
            $item = new Role();
            $item->name = $name;
            $item->description = $description;
            try {
                $am->add($item);
            } catch (Exception $e) {
                Yii::error($e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190926_114204_change_permissions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190926_114204_change_permissions cannot be reverted.\n";

        return false;
    }
    */
}
