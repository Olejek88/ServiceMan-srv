<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\db\Migration;

/**
 * Class m190718_095444_insert_new_templates
 */
class m190718_095444_insert_new_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->set('db', $this->db);

        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            ReferenceFunctions::loadReferencesAll($organisation['uuid'], $this->db);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_095444_insert_new_templates cannot be reverted.\n";

        return false;
    }
    */
}
