<?php

use common\models\Organization;
use common\models\TaskType;
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

        $this->dropColumn('task_template', 'normative');
        $this->addColumn('task_template', 'normative', $this->double()->defaultValue(1));

        //1 текущий ремонт const TASK_TYPE_CURRENT_REPAIR
        //2 плановый ремонт const TASK_TYPE_PLAN_REPAIR
        //3 текущий осмотр const TASK_TYPE_CURRENT_CHECK
        //4 внеочередной осмотр const TASK_TYPE_NOT_PLANNED_CHECK
        //5 сезонный осмотры const TASK_TYPE_SEASON_CHECK
        //6 плановое обслуживание const TASK_TYPE_PLAN_TO
        //7 внеплановое обслуживание const TASK_TYPE_NOT_PLAN_TO
        //8 устранение аварий const TASK_TYPE_REPAIR
        //9 контроль и поверка const TASK_TYPE_CONTROL
        //10 снятие показаний const TASK_TYPE_MEASURE
        //11 поверка const TASK_TYPE_POVERKA
        //12 монтаж const TASK_TYPE_INSTALL

        $this->insertIntoType('task_type',TaskType::TASK_TYPE_CURRENT_REPAIR, 'Текущий ремонт');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_PLAN_REPAIR, 'Плановый ремонт');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_CURRENT_CHECK, 'Текущий осмотр');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_NOT_PLANNED_CHECK, 'Внеочередной осмотр');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_SEASON_CHECK, 'Сезонный осмотр');

        $this->insertIntoType('task_type', TaskType::TASK_TYPE_VIEW, 'Осмотр');
        $this->insertIntoType('task_type', TaskType::TASK_TYPE_TO, 'Техобслуживание');
        //$this->insertIntoType('task_type',TaskType::TASK_TYPE_PLAN_TO, 'Плановое осблуживание');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_NOT_PLAN_TO, 'Внеплановое обслуживание');

        $this->insertIntoType('task_type', TaskType::TASK_TYPE_REPAIR, 'Устранение аварий');
        $this->insertIntoType('task_type', TaskType::TASK_TYPE_CONTROL, 'Контроль и проверка');
        $this->insertIntoType('task_type',TaskType::TASK_TYPE_MEASURE, 'Снятие показаний');

        $this->insertIntoType('task_type',TaskType::TASK_TYPE_POVERKA, 'Поверка');

        $this->insertIntoType('task_type', TaskType::TASK_TYPE_REPLACE, 'Замена');
        $this->insertIntoType('task_type', TaskType::TASK_TYPE_UNINSTALL, 'Демонтаж');
        //$this->insertIntoType('task_type', TaskType::TASK_TYPE_OVERHAUL, 'Технический ремонт');


        $this->insertIntoTaskTemplate('D1C0ED69-A5E7-4CAA-A48B-E03D854AF983', 'Локализация аварийных повреждений ХВС/ГВС',
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
            2, TaskType::TASK_TYPE_REPAIR, $currentTime, $currentTime);
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

    private function insertIntoType($table, $uuid, $title)
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    private function insertIntoTaskTemplate($uuid, $title, $description, $normative, $taskTypeUuid, $createdAt, $changedAt)
    {
        $this->insert('task_template', [
            'uuid' => $uuid,
            'title' => $title,
            'description' => $description,
            'normative' => $normative,
            'oid' => Organization::ORG_SERVICE_UUID,
            'taskTypeUuid' => $taskTypeUuid,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }
}
