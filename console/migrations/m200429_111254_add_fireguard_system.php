<?php

use common\models\EquipmentSystem;
use common\models\EquipmentType;
use yii\db\Migration;

/**
 * Class m200429_111254_add_fireguard_system
 */
class m200429_111254_add_fireguard_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%equipment_system}}', [
            'uuid' => EquipmentSystem::EQUIPMENT_SYSTEM_FIREGUARD,
            'title' => 'Пожарная система',
            'titleUser' => 'Рабочий пожарной системы',
        ]);

        $this->insert('{{%equipment_type}}', [
            'uuid' => EquipmentType::EQUIPMENT_FIREGUARD_BOX,
            'title' => 'Пожарный ящик',
            'equipmentSystemUuid' => EquipmentSystem::EQUIPMENT_SYSTEM_FIREGUARD,
        ]);

        $this->insert('{{%equipment_type}}', [
            'uuid' => EquipmentType::EQUIPMENT_FIREGUARD_BUTTON,
            'title' => 'Пожарная кнопка',
            'equipmentSystemUuid' => EquipmentSystem::EQUIPMENT_SYSTEM_FIREGUARD,
        ]);

        $this->insert('{{%equipment_type}}', [
            'uuid' => EquipmentType::EQUIPMENT_FIREGUARD_ALARM,
            'title' => 'Пожарная сигнализация',
            'equipmentSystemUuid' => EquipmentSystem::EQUIPMENT_SYSTEM_FIREGUARD,
        ]);

        $this->insert('{{%equipment_type}}', [
            'uuid' => EquipmentType::EQUIPMENT_FIREGUARD_SENSOR,
            'title' => 'Пожарный датчик',
            'equipmentSystemUuid' => EquipmentSystem::EQUIPMENT_SYSTEM_FIREGUARD,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200429_111254_add_fireguard_system cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200429_111254_add_fireguard_system cannot be reverted.\n";

        return false;
    }
    */
}
