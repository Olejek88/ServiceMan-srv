<?php

use common\models\IPermission;
use yii\db\Migration;

/**
 * Class m190927_111852_add_permission
 */
class m190927_111852_add_permission extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $am = Yii::$app->authManager;
        $models = [
            'common\models\Alarm',
            'common\models\AlarmStatus',
            'common\models\AlarmType',
            'common\models\City',
            'common\models\Contragent',
            'common\models\ContragentRegister',
            'common\models\ContragentType',
            'common\models\Defect',
            'common\models\DefectType',
            'common\models\Documentation',
            'common\models\DocumentationType',
            'common\models\Equipment',
            'common\models\EquipmentRegister',
            'common\models\EquipmentRegisterType',
            'common\models\EquipmentStatus',
            'common\models\EquipmentSystem',
            'common\models\EquipmentType',
            'common\models\ExportLink',
            'common\models\Gpstrack',
            'common\models\House',
            'common\models\HouseStatus',
            'common\models\HouseType',
            'common\models\Journal',
            'common\models\LoginForm',
            'common\models\Measure',
            'common\models\MeasureType',
            'common\models\Message',
            'common\models\ObjectContragent',
            'common\models\Objects',
            'common\models\ObjectStatus',
            'common\models\ObjectType',
            'common\models\Operation',
            'common\models\OperationTemplate',
            'common\models\Organization',
            'common\models\Photo',
            'common\models\Receipt',
            'common\models\Request',
            'common\models\RequestStatus',
            'common\models\RequestType',
            'common\models\Settings',
            'common\models\Shutdown',
            'common\models\Street',
            'common\models\TaskOperation',
            'common\models\Task',
            'common\models\TaskTemplateEquipment',
            'common\models\TaskTemplateEquipmentType',
            'common\models\TaskTemplate',
            'common\models\TaskType',
            'common\models\TaskUser',
            'common\models\TaskVerdict',
            'common\models\UserContragent',
            'common\models\UserHouse',
            'common\models\User',
            'common\models\Users',
            'common\models\UserSystem',
            'common\models\UserTokenAuth',
            'common\models\UserToken',
            'common\models\WorkStatus',
        ];

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
                $permissionName .= end($modelName);
                $permission = $am->createPermission($permissionName);
                $permission->description = $description;
                $am->add($permission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190927_111852_add_permission cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190927_111852_add_permission cannot be reverted.\n";

        return false;
    }
    */
}
