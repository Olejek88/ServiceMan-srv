<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m191113_065323_set_base_right
 */
class m191113_065323_set_base_right extends Migration
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
            ReferenceFunctions::fixOrgPermission($organisation->uuid, $this->db);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191113_065323_set_base_right cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191113_065323_set_base_right cannot be reverted.\n";

        return false;
    }
    */
}
