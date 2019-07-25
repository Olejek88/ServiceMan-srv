<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\db\Migration;

/**
 * Class m190725_060829_insert_new_templates
 */
class m190725_060829_insert_new_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->set('db', $this->db);
        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            ReferenceFunctions::loadReferencesAll2($organisation['uuid'],$this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_060829_insert_new_templates cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_060829_insert_new_templates cannot be reverted.\n";

        return false;
    }
    */
}
