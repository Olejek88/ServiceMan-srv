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

            $this->addColumn('{{%task_template_equipment_type}}', 'oid', $this->string(45)->notNull());
            // заполняем поле данными полученными косвенно через оборудование
            $cmd = $this->db->createCommand('
UPDATE task_template_equipment_type AS ttett
LEFT JOIN (
 SELECT ttett.taskTemplateUuid AS ttettuuid, ttt.oid AS tttoid FROM task_template_equipment_type AS ttett
 LEFT JOIN task_template AS ttt ON ttt.uuid=ttett.taskTemplateUuid
) AS subs ON ttett.taskTemplateUuid=subs.ttettuuid
SET ttett.oid=subs.tttoid

');
            $cmd->execute();
            $this->addForeignKey(
                'fk-task_template_equipment_type-organization-oid',
                '{{%task_template_equipment_type}}',
                'oid',
                '{{%organization}}',
                'uuid',
                $delete = 'RESTRICT',
                $update = 'CASCADE'
            );

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