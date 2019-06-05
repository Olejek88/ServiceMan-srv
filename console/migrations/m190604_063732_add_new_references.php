<?php

use common\models\TaskType;
use common\models\Users;
use yii\db\Migration;

/**
 * Class m190604_063732_add_new_references
 */
class m190604_063732_add_new_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
/*
        $this->dropColumn('task_template', 'normative');
        $this->addColumn('task_template', 'normative', $this->double()->defaultValue(1));

        $this->insertIntoType('task_type',TaskType::TASK_TYPE_VIEW,
            'Осмотр', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_CONTROL,
            'Контроль', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_REPAIR,
            'Ремонт', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_REPLACE,
            'Замена', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_INSTALL,
            'Установка', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_UNINSTALL,
            'Демонтаж', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_TO,
            'Техобслуживание', $currentTime, $currentTime);
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_OVERHAUL,
            'Технический ремонт', $currentTime, $currentTime);*/

        $this->insertIntoTaskTemplate('D1C0ED69-A5E7-4CAA-A48B-E03D854AF983','Локализация аварийных повреждений ХВС/ГВС',
            'Локализация аварийных повреждений внутридомовых инженерных систем холодного и горячего водоснабжения и водоотведения',
            0.5, TaskType::TASK_TYPE_VIEW, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('15B035EC-AF0C-424D-9A8C-543F22E0A63E',
            'Локализация аварийных повреждений внутридомовых систем отопления',
            'Локализация аварийных повреждений внутридомовых систем отопления',
            0.5, TaskType::TASK_TYPE_VIEW, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('970596BE-0BA3-49F3-A9C6-666E3FD2EE6F',
            'Ликвидация засоров внутридомовой инженерной системы водоотведения',
            'Ликвидация засоров внутридомовой инженерной системы водоотведения',
            2, TaskType::TASK_TYPE_REPAIR, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('3E5D09B6-6E83-4DD4-9CA2-89D7CF16FCCA',
            'Локализация аварийных повреждений электроснабжения',
            'Локализация аварийных повреждений электроснабжения',
            0.5, TaskType::TASK_TYPE_VIEW, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('C299C2CB-7475-4BE9-8EEC-5052A9232D37',
            'Ликвидацию засоров мусоропроводов внутри многоквартирных',
            'Ликвидацию засоров мусоропроводов внутри многоквартирных',
            23, TaskType::TASK_TYPE_REPAIR, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('A892957E-F63D-40F0-8D46-F21D6EF226AF',
            'Устранение аварийных повреждений внутридомовых систем',
            'Устранение аварийных повреждений внутридомовых инженерных систем холодного и горячего водоснабжения, водоотведения',
            72, TaskType::TASK_TYPE_REPAIR, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('0A6EC5B1-7A2E-4BCE-897F-1E2F2056CA2B',
            'Устранение аварийных повреждений внутридомовых систем отопления',
            'Устранение аварийных повреждений внутридомовых систем отопления',
            72, TaskType::TASK_TYPE_REPAIR, $currentTime, $currentTime);
        $this->insertIntoTaskTemplate('FFA96C9A-627E-4958-B69B-8F37F829FEB6',
            'Устранение аварийных повреждений внутридомовых систем  электроснабжения',
            'Устранение аварийных повреждений внутридомовых систем  электроснабжения',
            72, TaskType::TASK_TYPE_REPAIR, $currentTime, $currentTime);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190604_063732_add_new_references cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190604_063732_add_new_references cannot be reverted.\n";

        return false;
    }
    */

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    private function insertIntoTaskTemplate($uuid, $title, $description, $normative, $taskTypeUuid, $createdAt, $changedAt) {
        $this->insert('task_template', [
            'uuid' => $uuid,
            'title' => $title,
            'description' => $description,
            'normative' => $normative,
            'oid' => Users::ORGANISATION_UUID,
            'taskTypeUuid' => $taskTypeUuid,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }
}
