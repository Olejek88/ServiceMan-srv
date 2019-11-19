<?php

use yii\db\Migration;

/**
 * Class m191030_071819_fix_oid_taskTemplate_tables
 */
class m191030_071819_fix_oid_taskTemplate_tables extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->addColumn('{{%task_template_equipment}}', 'oid', $this->string(45)->notNull());
        // заполняем поле данными полученными косвенно через оборудование
        $cmd = $this->db->createCommand('
UPDATE task_template_equipment AS ttet
LEFT JOIN (
 SELECT ttet.taskTemplateUuid AS ttetuuid, ttt.oid AS tttoid FROM task_template_equipment AS ttet
 LEFT JOIN task_template AS ttt ON ttt.uuid=ttet.taskTemplateUuid
) AS subs ON ttet.taskTemplateUuid=subs.ttetuuid
SET ttet.oid=subs.tttoid

');
        $cmd->execute();
        $this->addForeignKey(
            'fk-task_template_equipment-organization-oid',
            '{{%task_template_equipment}}',
            'oid',
            '{{%organization}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191030_071819_fix_oid_taskTemplate_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191030_071819_fix_oid_taskTemplate_tables cannot be reverted.\n";

        return false;
    }
    */
}
