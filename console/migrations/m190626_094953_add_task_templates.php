<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use common\models\TaskType;
use yii\data\ActiveDataProvider;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m190626_094953_add_task_templates
 */
class m190626_094953_add_task_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$organisations = $this->db->createCommand('SELECT * FROM organization')->execute();
        Yii::$app->set('db', $this->db);

        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            ReferenceFunctions::loadReferences($organisation['uuid'], $this->db);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190626_094953_add_task_templates cannot be reverted.\n";

        return true;
    }
}