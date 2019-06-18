<?php

use common\models\ContragentType;
use yii\db\Migration;

/**
 * Class m190617_070258_new_changes
 */
class m190617_070258_new_changes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        $this->alterColumn('contragents','inn', $this->string()->defaultValue(''));
        $this->alterColumn('request_type','taskTemplateUuid', $this->string()->notNull());

        $this->createIndex(
            'idx-taskTemplateUuid',
            'request_type',
            'taskTemplateUuid'
        );

        $this->addForeignKey(
            'fk-request_task-taskTemplateUuid',
            'request_type',
            'taskTemplateUuid',
            'task_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $currentTime = date('Y-m-d\TH:i:s');
        $this->insertIntoRequestType('request_type','FB734A2A-220F-48D4-851F-A068462445C3',
            'Локализация аварийных повреждений ХВС/ГВС',
            'D1C0ED69-A5E7-4CAA-A48B-E03D854AF983',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','5712A90D-D759-4F99-BCC0-D4363F711027',
            'Локализация аварийных повреждений внутридомовых систем отопления',
            '15B035EC-AF0C-424D-9A8C-543F22E0A63E',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','A784D768-623A-41D3-9577-D4EDF2425F58',
            'Ликвидация засоров внутридомовой инженерной системы водоотведения',
            '970596BE-0BA3-49F3-A9C6-666E3FD2EE6F',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','1FA56BE9-0208-42B5-ACAE-89B1DE827A60',
            'Ликвидация засоров внутридомовой инженерной системы водоотведения',
            '3E5D09B6-6E83-4DD4-9CA2-89D7CF16FCCA',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','881AA443-FAA3-4AAB-B6FC-9C9B38BCACE6',
            'Локализация аварийных повреждений электроснабжения',
            '3E5D09B6-6E83-4DD4-9CA2-89D7CF16FCCA',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','E497A6C8-73B0-4995-9024-3A45FEE510B7',
            'Ликвидацию засоров мусоропроводов внутри многоквартирных',
            'C299C2CB-7475-4BE9-8EEC-5052A9232D37',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','80A889E3-9D48-477A-B6C1-96E295D89B82',
            'Устранение аварийных повреждений внутридомовых систем',
            'A892957E-F63D-40F0-8D46-F21D6EF226AF',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','F19A4548-1BCA-4F80-807F-667C8752DBA7',
            'Устранение аварийных повреждений внутридомовых систем отопления',
            '0A6EC5B1-7A2E-4BCE-897F-1E2F2056CA2B',
            $currentTime, $currentTime);
        $this->insertIntoRequestType('request_type','93D16E94-9027-402C-829D-4005A24A5074',
            'Устранение аварийных повреждений внутридомовых систем  электроснабжения',
            'FFA96C9A-627E-4958-B69B-8F37F829FEB6',
            $currentTime, $currentTime);

        $this->insertIntoType('contragent_type', ContragentType::WORKER,
            'Сотрудник', $currentTime, $currentTime);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190617_070258_new_changes cannot be reverted.\n";

        return false;
    }

    private function insertIntoRequestType($table, $uuid, $title, $taskTemplateUuid, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'taskTemplateUuid' => $taskTemplateUuid,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

}
