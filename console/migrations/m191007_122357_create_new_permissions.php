<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m191007_122357_create_new_permissions
 */
class m191007_122357_create_new_permissions extends Migration
{
    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function safeUp()
    {
        Yii::$app->set('db', $this->db);
        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            ReferenceFunctions::addOrgPermission($organisation->uuid, $this->db);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_122357_create_new_permissions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_122357_create_new_permissions cannot be reverted.\n";

        return false;
    }
    */
}
