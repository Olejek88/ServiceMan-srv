<?php

use yii\db\Migration;

/**
 * Class m180905_041835_add_references_record
 */
class m180905_041835_add_references_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertIntoReference('equipment_status','E681926C-F4A3-44BD-9F96-F0493712798D',
            'В порядке');
        $this->insertIntoReference('equipment_status','D5D31037-6640-4A8B-8385-355FC71DEBD7',
            'Неисправно');
        $this->insertIntoReference('equipment_status','A01B7550-4211-4D7A-9935-80A2FC257E92',
            'Отсутствует');

        $this->insertIntoReference('alarm_type','6FBD878D-1C49-41F4-B05E-90ABEF0153EB',
            'Незаконная врезка');
        $this->insertIntoReference('alarm_type','A29D4407-B1DE-4094-B4F5-838C7C8D335E',
            'Протечка воды');

        $this->insertIntoReference('alarm_status','4329BF34-D3D1-49AA-A8FC-C8A06E4C395A',
            'Обнаружено');
        $this->insertIntoReference('alarm_status','0AABB3A1-C8DD-490E-92F3-BDD996182ADD',
            'Устранена');
        $this->insertIntoReference('alarm_status','57CCC9A0-50F2-4432-BFF3-AE301CEBA50E',
            'Неизвестен');

        $this->insertIntoReference('house_status','9236E1FF-D967-4080-9F42-59B03ADD25E8',
            'В порядке');
        $this->insertIntoReference('house_status','559FBFE0-9543-4965-AC84-8919237EC317',
            'Не доступен');
        $this->insertIntoReference('house_status','9B6C8A1D-498E-40EE-B973-AA9ACC6322A0',
            'Отсутствует');

        $this->insertIntoReference('flat_status','32562AA9-DE1D-436D-A0ED-5F5789DB8712',
            'В порядке');
        $this->insertIntoReference('flat_status','FEA3CC91-DD48-4264-AEF6-F91947A1B8EB',
            'Не доступна');
        $this->insertIntoReference('flat_status','BB6E24F2-6FA5-4E9A-83C8-5E1F4D51789B',
            'Отсутствует');

        $this->insertIntoReference('equipment_type','7AB0B720-9FDB-448C-86C1-4649A7FCF279',
            'Счетчик ХВ');
        $this->insertIntoReference('equipment_type','4F50C767-A044-465B-A69F-02DD321BC5FB',
            'Счетчик ГВ');
        $this->insertIntoReference('equipment_type','B6904443-363B-4F01-B940-F47B463E66D8',
            'Электросчетчик');
        $this->insertIntoReference('equipment_type','42686CFC-34D0-45FF-95A4-04B0D865EC35',
            'Теплосчетчик');
    }


    private function insertIntoReference($reference, $uuid, $title)
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert($reference, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180905_041835_add_references_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180905_041835_add_references_record cannot be reverted.\n";

        return false;
    }
    */
}
