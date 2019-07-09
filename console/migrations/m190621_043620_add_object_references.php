<?php

use common\models\EquipmentSystem;
use common\models\EquipmentType;
use yii\db\Migration;

/**
 * Class m190621_043620_add_object_references
 */
class m190621_043620_add_object_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');

        $this->alterColumn('object_type','oid', $this->string(45));
        $this->dropForeignKey('fk-object_type-organization-oid','object_type');
        $this->dropColumn('object_type','oid');
        $this->dropColumn('object_type','gis_id');
/*
        $this->insertIntoType('object_type','80237148-9DBB-4315-A99D-D83CA5258C69',
            'Квартира', $currentTime, $currentTime);
        $this->insertIntoType('object_type','42686CFC-34D0-45FF-95A4-04B0D865EC35',
            'Общий', $currentTime, $currentTime);
        $this->insertIntoType('object_type','587B526B-A5C2-4B30-92DD-C63F796333A6',
            'Коммерческий', $currentTime, $currentTime);
        $this->insertIntoType('object_type','F68A562B-8F61-476F-A3E7-5666F9CEAFA1',
            'Входной', $currentTime, $currentTime);
*/
        $this->insertIntoType('object_type','CB9E9A67-FFE5-4168-8407-F2CAFBF76069',
            'Система ХВС', $currentTime, $currentTime);
        $this->insertIntoType('object_type','4923FFF8-B010-4043-90E6-C9665BDFBAD7',
            'Система ГВС', $currentTime, $currentTime);
        $this->insertIntoType('object_type','5C1711D5-5597-41FB-A32E-59C2AFB5E00B',
            'Система теплоснабжения', $currentTime, $currentTime);
        $this->insertIntoType('object_type','6A973F1E-1A6D-4C64-B55C-3EE4FB149C5E',
            'Кровля и водоотвод', $currentTime, $currentTime);
        $this->insertIntoType('object_type','A2DA436F-7230-48B2-8991-3913DA5DFB39',
            'Внешний фасад', $currentTime, $currentTime);
        $this->insertIntoType('object_type','FFDAC354-66CF-41CB-9820-E8328B426D32',
            'Придомовая территория', $currentTime, $currentTime);
        $this->insertIntoType('object_type','73EF98B1-8B3F-4F29-96E1-772A4959AC1F',
            'Подъезд', $currentTime, $currentTime);
        $this->insertIntoType('object_type','0D6ABB06-C170-4E03-B7C7-58DD8A3B7FCD',
            'Система канализации', $currentTime, $currentTime);

        $this->insertIntoType('object_type','49650A0E-3C02-43F8-9D92-830EA329B93B',
            'Электричество', $currentTime, $currentTime);
        $this->insertIntoType('object_type','5EFD55F0-4E33-49FB-85A6-CF7873662E01',
            'Система вентиляции', $currentTime, $currentTime);
        $this->insertIntoType('object_type','FDBAF46A-1764-4F25-A2B0-AC66C5102E2F',
            'Система газоснабжения', $currentTime, $currentTime);
        $this->insertIntoType('object_type','3D163AC7-3061-4796-B535-0B39C08E9377',
            'Подвал', $currentTime, $currentTime);

        $this->insertIntoEquipmentSystem('equipment_system', EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Электрика','Электрик', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_GAS,
            'Газовое оборудование', 'Газовщик', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Система водоснабжения','Сантехник', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_HEAT,
            'Система теплоснабжения','Теплотехник', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_ROOF,
            'Кровельные системы','Кровельщик', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_WALL,
            'Фасадные системы','Фасадный рабочий', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Строительные системы','Строитель', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_MAIN,
            'Другие системы','Разнорабочий', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_VENT,
            'Система вентиляции и кондиционирования','Ветиляция и кондиционирование', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_LIFT,
            'Лифтовое хозяйство','Лифтер', $currentTime, $currentTime);
        $this->insertIntoEquipmentSystem('equipment_system',EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Слаботочные системы','Техник', $currentTime, $currentTime);

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HVS_MAIN,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Водомерный узел ХВС');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_TYPE_BALCONY,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Балконные конструкции');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Электроплита');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HVS_COUNTER,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Воодосчетчик ХВС');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HVS_PUMP,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Насосная станция ХВС');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HVS_TOWER,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Стояки ХВС');


        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_GVS_TOWER,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Стояки ГВС');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_GVS_MAIN,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Главный узел ГВС и розливы');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_GVS_PUMP,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Циркуляционный насос ГВС');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HEAT_MAIN,EquipmentSystem::EQUIPMENT_SYSTEM_HEAT,
            'Тепловой пункт');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HEAT_TOWER,EquipmentSystem::EQUIPMENT_SYSTEM_HEAT,
            'Стояки теплоснабжения');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HEAT_RADIATOR,EquipmentSystem::EQUIPMENT_SYSTEM_HEAT,
            'Батарея');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HEAT_COUNTER,EquipmentSystem::EQUIPMENT_SYSTEM_HEAT,
            'Теплосчетчик и КиП');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_HEAT_PUMP,EquipmentSystem::EQUIPMENT_SYSTEM_HEAT,
            'Циркуляционный насос');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ROOF,EquipmentSystem::EQUIPMENT_SYSTEM_ROOF,
            'Кровля (парапеты и карнизы)');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ROOF_ENTRANCE,EquipmentSystem::EQUIPMENT_SYSTEM_ROOF,
            'Выходы чердачного помещения');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ROOF_ROOM,EquipmentSystem::EQUIPMENT_SYSTEM_ROOF,
            'Чердак');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ROOF_WATER_PIPE,EquipmentSystem::EQUIPMENT_SYSTEM_ROOF,
            'Система водоотвода');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_WALL,EquipmentSystem::EQUIPMENT_SYSTEM_WALL,
            'Стены, конструкции и перекрытия');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_WALL_WATER,EquipmentSystem::EQUIPMENT_SYSTEM_WALL,
            'Водостоки');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_YARD,EquipmentSystem::EQUIPMENT_SYSTEM_MAIN,
            'Придомовая территория');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_YARD_DRENAGE,EquipmentSystem::EQUIPMENT_SYSTEM_MAIN,
            'Дренажная система');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_YARD_TBO,EquipmentSystem::EQUIPMENT_SYSTEM_MAIN,
            'Площадки для ТБО');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Окна');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ENTRANCE_DOOR,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Дверь подъезда');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Мусоропровод');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ENTRANCE_STAIRS,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Лестничная клетка');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_LIFT,EquipmentSystem::EQUIPMENT_SYSTEM_LIFT,
            'Лифт');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Дверь тамбура');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ENTRANCE_MAIN,EquipmentSystem::EQUIPMENT_SYSTEM_BUILD,
            'Входная группа');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_SEWER_PIPE,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Стояки канализации');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_SEWER_MAIN,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Основной узел (лежневка и выпуск)');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_SEWER_WELL,EquipmentSystem::EQUIPMENT_SYSTEM_SANTECH,
            'Колодец канализации');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Электросчетчик');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_VRU,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'ВРУ');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Этажный щиток');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Осветительные приборы');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Освещение подъездов');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE,EquipmentSystem::EQUIPMENT_SYSTEM_ELECTRO,
            'Стояки с проводкой');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_INTERNET,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Стояки с проводкой');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_TV,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Телевидение');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_CONDITIONER,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Вениляционные каналы');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_DOMOPHONE,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Домофоны');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_GAS,EquipmentSystem::EQUIPMENT_SYSTEM_GAS,
            'Система газоснабжения');

        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_BASEMENT,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Фундамент');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_BASEMENT_ROOM,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Помещение');
        $this->insertIntoEquipmentType(EquipmentType::EQUIPMENT_BASEMENT_WINDOWS,EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
            'Окна в подвал');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190621_043620_add_object_references cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    private function insertIntoEquipmentSystem($table, $uuid, $title, $titleUser, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'titleUser' => $titleUser,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
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
