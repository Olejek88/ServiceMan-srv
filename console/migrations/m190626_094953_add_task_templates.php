<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use common\models\TaskType;
use yii\db\Migration;

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
        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            ReferenceFunctions::loadReferences($organisation['uuid']);
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
