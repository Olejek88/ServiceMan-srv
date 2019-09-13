<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m190730_121226_fix_request_type
 */
class m190730_121226_fix_request_type extends Migration
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
            if ($organisation['uuid']!=Organization::ORG_SERVICE_UUID)
                ReferenceFunctions::loadRequestTypes($organisation['uuid'], $this->db);
        }
        $this->renameColumn('request', 'userUuid', 'contragentUuid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190730_121226_fix_request_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190730_121226_fix_request_type cannot be reverted.\n";

        return false;
    }
    */
}
