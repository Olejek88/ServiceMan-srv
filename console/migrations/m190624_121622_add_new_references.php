<?php

use common\models\EquipmentSystem;
use common\models\EquipmentType;
use yii\db\Migration;

/**
 * Class m190624_121622_add_new_references
 */
class m190624_121622_add_new_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_HERD, EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Электроплита');
/*        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            ReferenceFunctions::loadReferences($organisation['uuid']);
        }*/
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190624_121622_add_new_references cannot be reverted.\n";

        return true;
    }

    private function insertIntoEquipmentType($uuid, $equipmentSystemUuid, $title) {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert('equipment_type', [
            'uuid' => $uuid,
            'title' => $title,
            'equipmentSystemUuid' => $equipmentSystemUuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }


}
