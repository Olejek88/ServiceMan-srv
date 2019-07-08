<?php

namespace common\components;

use app\commands\MainFunctions;
use common\models\EquipmentType;
use common\models\HouseType;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipmentType;
use common\models\TaskType;

class ReferenceFunctions
{
    public static function loadReferences($oid)
    {
        self::insertIntoHouseType(HouseType::HOUSE_TYPE_MKD,
            'Многокваритирный дом', $oid);
        self::insertIntoHouseType(HouseType::HOUSE_TYPE_COMMERCE,
            'Коммерческий объект', $oid);
        self::insertIntoHouseType(HouseType::HOUSE_TYPE_BUDGET,
            'Бюджетное учереждение', $oid);

        self::insertIntoTaskTemplate('Проверка межэтажных перекрытий',
            'Проверка межэтажных перекрытий', 8,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Ремонт межэтажных перекрытий',
            'Ремонт межэтажных перекрытий', 96,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Окраска',
            'Окраска', 48,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Герметизация и утепление',
            'Герметизация и утепление', 120,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);

        self::insertIntoTaskTemplate('Техническое обслуживание',
            'Техническое обслуживание', 2,
            TaskType::TASK_TYPE_TO,
            EquipmentType::EQUIPMENT_ELECTRICITY_HERD,
             $oid);

        self::insertIntoTaskTemplate('Общий осмотр тех.состояния',
            'Общий осмотр технического состояния', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Контроль параметров',
            'Общий осмотр технического состояния', 2,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Проверка состояния КИП',
            'Проверка состояния КИП', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Замена КИП',
            'Замена КИП', 2,
            TaskType::TASK_TYPE_REPLACE, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Промывка системы',
            'Промывка системы', 2,
            TaskType::TASK_TYPE_TO, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Удаление коррозионных отложений',
            'Удаление коррозионных отложений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Проверка исправности запорно-регулирующей арматуры',
            'Проверка исправности запорно-регулирующей арматуры', 2,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Устранение аварийных повреждений',
            'Устранение аварийных повреждений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Текущий ремонт',
            'Текущий ремонт', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Планово-предупредительный ремонт',
            'Планово-предупредительный ремонт', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);

        self::insertIntoTaskTemplate('Общий осмотр',
            'Общий осмотр', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplate('Устранение аварийных повреждений',
            'Устранение аварийных повреждений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
    }

    public static function loadReferencesNext($oid)
    {
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

        self::insertIntoTaskTemplate('Проверка межэтажных перекрытий',
            'Проверка межэтажных перекрытий', 8,
            TaskType::TASK_TYPE_CURRENT_CHECK, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Проверка межэтажных перекрытий',
            'Проверка межэтажных перекрытий', 8,
            TaskType::TASK_TYPE_NOT_PLANNED_CHECK, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Проверка межэтажных перекрытий',
            'Проверка межэтажных перекрытий', 8,
            TaskType::TASK_TYPE_SEASON_CHECK, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);



        self::insertIntoTaskTemplate('Ремонт межэтажных перекрытий',
            'Ремонт межэтажных перекрытий', 96,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Окраска',
            'Окраска', 48,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate('Герметизация и утепление',
            'Герметизация и утепление', 120,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);

        self::insertIntoTaskTemplate('Техническое обслуживание',
            'Техническое обслуживание', 2,
            TaskType::TASK_TYPE_TO,
            EquipmentType::EQUIPMENT_ELECTRICITY_HERD,
            $oid);

        self::insertIntoTaskTemplate('Общий осмотр тех.состояния',
            'Общий осмотр технического состояния', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Контроль параметров',
            'Общий осмотр технического состояния', 2,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Проверка состояния КИП',
            'Проверка состояния КИП', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Замена КИП',
            'Замена КИП', 2,
            TaskType::TASK_TYPE_REPLACE, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Промывка системы',
            'Промывка системы', 2,
            TaskType::TASK_TYPE_TO, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Удаление коррозионных отложений',
            'Удаление коррозионных отложений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Проверка исправности запорно-регулирующей арматуры',
            'Проверка исправности запорно-регулирующей арматуры', 2,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Устранение аварийных повреждений',
            'Устранение аварийных повреждений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Текущий ремонт',
            'Текущий ремонт', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate('Планово-предупредительный ремонт',
            'Планово-предупредительный ремонт', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);

        self::insertIntoTaskTemplate('Общий осмотр',
            'Общий осмотр', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplate('Устранение аварийных повреждений',
            'Устранение аварийных повреждений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
    }

    private static function insertIntoHouseType($uuid, $title, $organizationUuid) {
        $houseType = new HouseType();
        $houseType->uuid = $uuid;
        $houseType->title = $title;
        $houseType->oid = $organizationUuid;
        $houseType->save();
    }

    private static function insertIntoTaskTemplate($title, $description, $normative, $taskTypeUuid, $equipmentTypeUuid,
                                                   $organizationUuid) {
        $taskTemplate = new TaskTemplate();
        $taskTemplate->uuid = MainFunctions::GUID();
        $taskTemplate->title = $title;
        $taskTemplate->description = $description;
        $taskTemplate->normative = $normative;
        $taskTemplate->oid = $organizationUuid;
        $taskTemplate->taskTypeUuid = $taskTypeUuid;
        $taskTemplate->save();

        $taskTemplateEquipmentType = new TaskTemplateEquipmentType();
        $taskTemplateEquipmentType->equipmentTypeUuid = $equipmentTypeUuid;
        $taskTemplateEquipmentType->taskTemplateUuid = $taskTemplate->uuid;
        $taskTemplateEquipmentType->uuid = MainFunctions::GUID();
        $taskTemplateEquipmentType->save();

    }
}

