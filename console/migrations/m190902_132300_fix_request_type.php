<?php

use common\components\MainFunctions;
use common\models\Organization;
use yii\db\Migration;

/**
 * Class m190902_132300_fix_request_type
 */
class m190902_132300_fix_request_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%request_type}}', 'oid', $this->string(45));
        $this->update('{{%request_type}}', [
            'oid' => Organization::ORG_SERVICE_UUID,
        ]);
        $this->alterColumn('{{%request_type}}', 'oid', $this->string(45)->notNull());
        $this->addForeignKey(
            'fk-request-type-organization-oid',
            '{{%request_type}}',
            'oid',
            '{{%organization}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->dropForeignKey('fk-request_task-taskTemplateUuid', '{{%request_type}}');
        $this->alterColumn('{{%request_type}}', 'taskTemplateUuid', $this->string(45)->null());
        $this->addForeignKey(
            'fk-request_type-taskTemplateUuid',
            'request_type',
            'taskTemplateUuid',
            'task_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert('request_type', [
            'uuid' => MainFunctions::GUID(),
            'oid' => Organization::ORG_SERVICE_UUID,
            'title' => 'Другой характер обращения',
            'taskTemplateUuid' => null,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190902_132300_fix_request_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190902_132300_fix_request_type cannot be reverted.\n";

        return false;
    }
    */
}
