<?php

namespace common\components;

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

    public static function loadReferencesAll($oid)
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
        self::insertIntoTaskTemplateNew('Проверка межэтажных перекрытий',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplateNew('Ремонт межэтажных перекрытий',
            8, [1,2,8], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplateNew('Окраска',
            8, [6,7], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplateNew('Герметизация и утепление',
            8, [6,7], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);

        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [1,2,6,7,9], EquipmentType::EQUIPMENT_ELECTRICITY_HERD, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр технического состояния',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Контроль параметров',
            8, [9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Проверка состояния КиП',
            8, [9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Замена элементов',
            8, [12], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Проверка исправности и запорно-регулирующей аппаратуры',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Планово-предупредительный ремонт',
            8, [2], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_HVS_TOWER, $oid);

        self::insertIntoTaskTemplateNew('Проверка состояния насоса',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HVS_PUMP, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [1,2,6,7,12], EquipmentType::EQUIPMENT_HVS_PUMP, $oid);

        self::insertIntoTaskTemplateNew('Снятие показаний',
            8, [10], EquipmentType::EQUIPMENT_HVS_COUNTER, $oid);
        self::insertIntoTaskTemplateNew('Поверка',
            8, [11], EquipmentType::EQUIPMENT_HVS_COUNTER, $oid);
        self::insertIntoTaskTemplateNew('Замена',
            8, [11], EquipmentType::EQUIPMENT_HVS_COUNTER, $oid);

        self::insertIntoTaskTemplateNew('Осмотр изоляционного покрытия',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Восстановление изоляционного покрытия',
            8, [6,7], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Общий осмотр технического состояния',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Контроль параметров',
            8, [9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Проверка состояния КиП',
            8, [9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Замена элементов',
            8, [12], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Опрессовка системы',
            8, [6,7], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Проверка исправности и запорно-регулирующей аппаратуры',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Планово-предупредительный ремонт',
            8, [2], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Осмотр изоляционного покрытия',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Восстановление изоляционного покрытия',
            8, [1,2], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);

        self::insertIntoTaskTemplateNew('Проверка состояния насоса',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_GVS_PUMP, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [1,2,6,7,12], EquipmentType::EQUIPMENT_GVS_PUMP, $oid);

        self::insertIntoTaskTemplateNew('Осмотр изоляционного покрытия',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Восстановление изоляционного покрытия',
            8, [6,7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Общий осмотр технического состояния',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Контроль параметров',
            8, [9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Проверка состояния КиП',
            8, [9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Замена элементов',
            8, [12], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Опрессовка системы',
            8, [6,7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Пуско-наладочные работы',
            8, [6,7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Проверка исправности и запорно-регулирующей аппаратуры',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Планово-предупредительный ремонт',
            8, [2], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Осмотр изоляционного покрытия',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Восстановление изоляционного покрытия',
            8, [1,2], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр систем отопления подъезда',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_HEAT_RADIATOR, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HEAT_RADIATOR, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_HEAT_RADIATOR, $oid);

        self::insertIntoTaskTemplateNew('Снятие показаний',
            8, [10], EquipmentType::EQUIPMENT_HEAT_COUNTER, $oid);
        self::insertIntoTaskTemplateNew('Поверка',
            8, [11], EquipmentType::EQUIPMENT_HEAT_COUNTER, $oid);
        self::insertIntoTaskTemplateNew('Замена',
            8, [11], EquipmentType::EQUIPMENT_HEAT_COUNTER, $oid);

        self::insertIntoTaskTemplateNew('Окраска',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew('Осмотр и проверка кровли',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew('Ремонт и восстановление',
            8, [1,2,8], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew('Осмотр парапета',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew('Очистка',
            8, [6], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew('Замена кровельного покрытия',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew('Очистка от снежного покрова',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF, $oid);

        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew('Осмотр люка и запорных устройств',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew('Ремонт люка и запорных устройств',
            8, [1], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);

        self::insertIntoTaskTemplateNew('Уборка',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Осмотр конструкций, перегородок и перекрытий',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2,8], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка заземления, оборудования крыши',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка температурного-влажностного режима',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Дератизация и дезинфекция',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка на соответствие пожарной безопасности',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка слуховых окон',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);

        self::insertIntoTaskTemplateNew('Осмотр',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2,8], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew('Осмотр',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);

        self::insertIntoTaskTemplateNew('Осмотр тех. состояния отмостки',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT, $oid);
        self::insertIntoTaskTemplateNew('Ремонт отмостки',
            8, [1,2], EquipmentType::EQUIPMENT_BASEMENT, $oid);
        self::insertIntoTaskTemplateNew('Осмотр тех. состояния видимых частей конструкций',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT, $oid);
        self::insertIntoTaskTemplateNew('Восстановление эксплуатационных свойств конструкций',
            8, [1,2,8], EquipmentType::EQUIPMENT_BASEMENT, $oid);

        self::insertIntoTaskTemplateNew('Осмотр конструкций',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт конструкций',
            8, [1,2,8], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Отделочные работы',
            8, [6,7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Работы с указателями, нумерацией и инф.досками',
            8, [7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Осмотр наличия указателей, нумерации и инф.досками',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Теплоизоляционные работы',
            8, [6,7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Гидроизоляционные работы',
            8, [6,7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Осмотр отделки фасада',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew('Устранение критичных повреждений',
            8, [8], EquipmentType::EQUIPMENT_WALL, $oid);

        self::insertIntoTaskTemplateNew('Осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_WALL_WATER, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2,8], EquipmentType::EQUIPMENT_WALL_WATER, $oid);
        self::insertIntoTaskTemplateNew('Очистка',
            8, [6,7], EquipmentType::EQUIPMENT_WALL_WATER, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_WALL_WATER, $oid);

        self::insertIntoTaskTemplateNew('Осмотр асфальтового покрытия',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Ремонт асфальтового покрытия',
            8, [1,2], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Осмотр состояния детской площадки и газонов',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Озеленение',
            8, [6,7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Текущие работы по облагораживанию детской площадки',
            8, [6,7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Уборка снега, наледи и обработка пескосоляной смесью',
            8, [6,7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Уборка',
            8, [6,7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew('Прочие работы',
            8, [6,7], EquipmentType::EQUIPMENT_YARD, $oid);

        self::insertIntoTaskTemplateNew('Проверка состояния гидроизоляции систем водоотвода',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);
        self::insertIntoTaskTemplateNew('Восстановление состояния гидроизоляции систем водоотвода',
            8, [1,2,8], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);
        self::insertIntoTaskTemplateNew('Прочистка ливневой канализации',
            8, [6,7], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);
        self::insertIntoTaskTemplateNew('Очистка системы',
            8, [6,7], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);

        self::insertIntoTaskTemplateNew('Очистка',
            8, [6,7], EquipmentType::EQUIPMENT_YARD_TBO, $oid);

        self::insertIntoTaskTemplateNew('Осмотр оконных блоков',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Замена стекол и ремонт',
            8, [1,2,8], EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS, $oid);

        self::insertIntoTaskTemplateNew('Осмотр двери и запорных устройств',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_DOOR, $oid);
        self::insertIntoTaskTemplateNew('Ремонт двери и запорных устройств',
            8, [1,2,8], EquipmentType::EQUIPMENT_ENTRANCE_DOOR, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_DOOR, $oid);

        self::insertIntoTaskTemplateNew('Осмотр состояния загрузочных клапанов',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Ремонт',
            8, [1,2], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Проверка состояния стволов',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Проверка состояния мусоросборника',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Срочная ликвидация засора',
            8, [8], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Общий осмотр системы',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);

        self::insertIntoTaskTemplateNew('Осмотр ограждений',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Осмотр несущих конструкций и перекрытий',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт ограждений',
            8, [1,8], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт несущих конструкций и перекрытий',
            8, [1,8], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Отделочные работы',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Работы по нумерации',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Осмотр состояния отделки стен',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Осмотр состояния отделки потолка',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Осмотр состояния отделки пола',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Уборка',
            8, [6], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Окраска косоуров и металлических элементов',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Дератизация и дезинфекция',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Проверка на соответствие пожарной безопасности',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Периодический ремонт',
            8, [2], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);

        self::insertIntoTaskTemplateNew('Осмотр кабины и проверка связи',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew('Проверка сроков эксплуатации',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew('Осмотр состояния дверей лифта',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew('Осмотр предмашинного отделения',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew('Аварийный ремонт',
            8, [8], EquipmentType::EQUIPMENT_LIFT, $oid);

        self::insertIntoTaskTemplateNew('Осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR, $oid);
        self::insertIntoTaskTemplateNew('Ремонт',
            8, [1], EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR, $oid);

        self::insertIntoTaskTemplateNew('Осмотр козырька',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт козырька',
            8, [1,2], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Осмотр перил, крыльца и пандуса',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Уборка',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Уборка снега и наледи',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Срочная ликвидация засора',
            8, [8], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Срочная ликвидация засора',
            8, [8], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);
        self::insertIntoTaskTemplateNew('Техническое обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_SEWER_WELL, $oid);

        self::insertIntoTaskTemplateNew('Снятие показаний',
            8, [10], EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER, $oid);
        self::insertIntoTaskTemplateNew('Поверка',
            8, [11], EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER, $oid);
        self::insertIntoTaskTemplateNew('Замена',
            8, [11], EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER, $oid);

        self::insertIntoTaskTemplateNew('Проверка запирающих устройств',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew('Ремонт запирающих устройств',
            8, [1], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew('Проверка заземления',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew('Проверка устройств защитного отключения',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew('Отключение',
            8, [6,7,8], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);

        self::insertIntoTaskTemplateNew('Проверка запирающих устройств',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);
        self::insertIntoTaskTemplateNew('Ремонт запирающих устройств',
            8, [1], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);
        self::insertIntoTaskTemplateNew('Отключение',
            8, [6,7,8], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);

        self::insertIntoTaskTemplateNew('Осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,12], EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT, $oid);
        self::insertIntoTaskTemplateNew('Очистка',
            8, [6,7], EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT, $oid);

        self::insertIntoTaskTemplateNew('Осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,12], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT, $oid);
        self::insertIntoTaskTemplateNew('Очистка',
            8, [6,7], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT, $oid);

        self::insertIntoTaskTemplateNew('Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE, $oid);

        self::insertIntoTaskTemplateNew('Уборка',
            8, [6,7], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка температурно-влажностного режима',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Осмотр конструкций, перегородок и перекрытий',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2,8], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Дератизация и дезинфекция',
            8, [6,7], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка на соответствие пожарной безопасности',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Проверка загазованности',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);

        self::insertIntoTaskTemplateNew('Общий осмотр',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Текущий ремонт',
            8, [1,2], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Проверка ограждений',
            8, [3,4,5,9], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Восстановление ограждений',
            8, [1,2], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Текущее обслуживание',
            8, [6,7], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('Уборка',
            8, [6,7], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew('очистка от снежного покрова',
            8, [6,7], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
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
        $present = TaskTemplate::find()->where(['taskTypeUuid' => $taskTemplate->taskTypeUuid])
            ->andWhere(['oid' => $organizationUuid])
            ->andWhere(['title' => $title])->one();
        if (!$present) {
            $taskTemplate->save();

            $taskTemplateEquipmentType = new TaskTemplateEquipmentType();
            $taskTemplateEquipmentType->equipmentTypeUuid = $equipmentTypeUuid;
            $taskTemplateEquipmentType->taskTemplateUuid = $taskTemplate->uuid;
            $taskTemplateEquipmentType->uuid = MainFunctions::GUID();
            $taskTemplateEquipmentType->save();
        }
    }

    private static function insertIntoTaskTemplateNew($title, $normative, $taskTypes, $equipmentTypeUuid, $organizationUuid) {
        foreach ($taskTypes as $taskType) {
            $taskTemplate = new TaskTemplate();
            $taskTemplate->uuid = MainFunctions::GUID();
            $taskTemplate->title = $title;
            $taskTemplate->description = $title;
            $taskTemplate->normative = $normative;
            $taskTemplate->oid = $organizationUuid;
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
            switch ($taskType) {
                case 1: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_CURRENT_REPAIR; break;
                case 2: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_PLAN_REPAIR; break;
                case 3: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_CURRENT_CHECK; break;
                case 4: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_NOT_PLANNED_CHECK; break;
                case 5: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_SEASON_CHECK; break;
                case 6: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_PLAN_TO; break;
                case 7: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_NOT_PLAN_TO; break;
                case 8: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_REPAIR; break;
                case 9: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_CONTROL; break;
                case 10: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_MEASURE; break;
                case 11: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_POVERKA; break;
                case 12: $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_INSTALL; break;
                default:
                    $taskTemplate->taskTypeUuid = TaskType::TASK_TYPE_CURRENT_REPAIR;
            }
            $taskTemplateUuid = $taskTemplate->uuid;
            $present = TaskTemplate::find()
                ->where(['taskTypeUuid' => $taskTemplate->taskTypeUuid])
                ->andWhere(['title' => $title])
                ->andWhere(['oid' => $organizationUuid])
                ->one();
            if (!$present) {
                $taskTemplate->save();
            } else {
                $taskTemplateUuid = $present['uuid'];
            }

            $present = TaskTemplateEquipmentType::find()
                ->where(['taskTemplateUuid' => $taskTemplateUuid])
                ->andWhere(['equipmentTypeUuid' => $equipmentTypeUuid])
                ->one();
            if (!$present) {
                $taskTemplateEquipmentType = new TaskTemplateEquipmentType();
                $taskTemplateEquipmentType->equipmentTypeUuid = $equipmentTypeUuid;
                $taskTemplateEquipmentType->taskTemplateUuid = $taskTemplateUuid;
                $taskTemplateEquipmentType->uuid = MainFunctions::GUID();
                $taskTemplateEquipmentType->save();
            }
        }
    }
}

