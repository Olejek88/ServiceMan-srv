<?php

use common\components\MainFunctions;
use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m190913_042018_some_logic_change
 */
class m190913_042018_some_logic_change extends Migration
{
    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        Yii::$app->set('db', $this->db);
        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            if ($organisation['uuid'] != Organization::ORG_SERVICE_UUID)
                ReferenceFunctions::loadReferences1($organisation['uuid'], $this->db);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190913_042018_some_logic_change cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190913_042018_some_logic_change cannot be reverted.\n";

        return false;
    }
    */
}
