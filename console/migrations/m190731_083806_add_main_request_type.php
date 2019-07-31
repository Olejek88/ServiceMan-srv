<?php

use common\components\MainFunctions;
use common\models\Organization;
use common\models\RequestType;
use common\models\TaskTemplate;
use common\models\TaskType;
use yii\db\Migration;

/**
 * Class m190731_083806_add_main_request_type
 */
class m190731_083806_add_main_request_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
        Yii::$app->set('db', $this->db);
        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            $this->insert('task_template', [
                'uuid' => TaskTemplate::DEFAULT_TASK,
                'title' => 'Задача по-умолчанию',
                'description' => 'Задача по-умолчанию',
                'normative' => 0,
                'oid' => $organisation['uuid'],
                'taskTypeUuid' => TaskType::TASK_TYPE_VIEW,
                'createdAt' => $currentTime,
                'changedAt' => $currentTime
            ]);
        }
        $this->insert('request_type', [
            'uuid' => RequestType::GENERAL,
            'title' => 'Другой характер обращения',
            'taskTemplateUuid' => TaskTemplate::DEFAULT_TASK,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190731_083806_add_main_request_type cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190731_083806_add_main_request_type cannot be reverted.\n";

        return false;
    }
    */
}
