<?php

namespace common\components;

use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\Organization;
use common\models\TaskType;
use yii\db\Connection;
use yii\db\Exception;

class ReferenceFunctions
{
    /**
     * @param $oid
     * @param $db Connection
     * @throws Exception
     */
    public static function loadReferences($oid, $db)
    {
//        self::insertIntoHouseType($db, HouseType::HOUSE_TYPE_MKD,
//            'Многоквартирный дом', $oid);
//        self::insertIntoHouseType($db, HouseType::HOUSE_TYPE_COMMERCE,
//            'Коммерческий объект', $oid);
//        self::insertIntoHouseType($db, HouseType::HOUSE_TYPE_BUDGET,
//            'Бюджетное учереждение', $oid);

        self::insertIntoTaskTemplate($db, 'Проверка межэтажных перекрытий',
            'Проверка межэтажных перекрытий', 8,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate($db, 'Ремонт межэтажных перекрытий',
            'Ремонт межэтажных перекрытий', 96,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate($db, 'Окраска',
            'Окраска', 48,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplate($db, 'Герметизация и утепление',
            'Герметизация и утепление', 120,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);

        self::insertIntoTaskTemplate($db, 'Техническое обслуживание',
            'Техническое обслуживание', 2,
            TaskType::TASK_TYPE_TO,
            EquipmentType::EQUIPMENT_ELECTRICITY_HERD,
            $oid);

        self::insertIntoTaskTemplate($db, 'Общий осмотр тех.состояния',
            'Общий осмотр технического состояния', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Контроль параметров',
            'Общий осмотр технического состояния', 2,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Проверка состояния КИП',
            'Проверка состояния КИП', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Замена КИП',
            'Замена КИП', 2,
            TaskType::TASK_TYPE_REPLACE, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Промывка системы',
            'Промывка системы', 2,
            TaskType::TASK_TYPE_TO, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Удаление коррозионных отложений',
            'Удаление коррозионных отложений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Проверка исправности запорно-регулирующей арматуры',
            'Проверка исправности запорно-регулирующей арматуры', 2,
            TaskType::TASK_TYPE_CONTROL, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Устранение аварийных повреждений',
            'Устранение аварийных повреждений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Текущий ремонт',
            'Текущий ремонт', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplate($db, 'Планово-предупредительный ремонт',
            'Планово-предупредительный ремонт', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_MAIN, $oid);

        self::insertIntoTaskTemplate($db, 'Общий осмотр',
            'Общий осмотр', 2,
            TaskType::TASK_TYPE_VIEW, EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplate($db, 'Устранение аварийных повреждений',
            'Устранение аварийных повреждений', 2,
            TaskType::TASK_TYPE_REPAIR, EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
    }

    /**
     * @param $oid
     * @param $db Connection
     * @throws Exception
     */
    public static function loadReferencesNext($oid, $db)
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

        self::insertIntoTaskTemplate($db, 'Техническое обслуживание',
            'Техническое обслуживание', 2,
            TaskType::TASK_TYPE_TO,
            EquipmentType::EQUIPMENT_ELECTRICITY_HERD,
            $oid);
    }

    /**
     * @param $oid
     * @param $db Connection
     * @throws Exception
     */
    public static function loadReferencesAll($oid, $db)
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
        self::insertIntoTaskTemplateNew($db, 'Проверка межэтажных перекрытий',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт межэтажных перекрытий',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplateNew($db, 'Окраска',
            8, [6, 7], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);
        self::insertIntoTaskTemplateNew($db, 'Герметизация и утепление',
            8, [6, 7], EquipmentType::EQUIPMENT_TYPE_BALCONY, $oid);

        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [1, 2, 6, 7, 9], EquipmentType::EQUIPMENT_ELECTRICITY_HERD, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр технического состояния',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Контроль параметров',
            8, [9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка состояния КиП',
            8, [9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена элементов',
            8, [12], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка исправности и запорно-регулирующей аппаратуры',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Планово-предупредительный ремонт',
            8, [2], EquipmentType::EQUIPMENT_HVS_MAIN, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_HVS_TOWER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Проверка состояния насоса',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HVS_PUMP, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [1, 2, 6, 7, 12], EquipmentType::EQUIPMENT_HVS_PUMP, $oid);

        self::insertIntoTaskTemplateNew($db, 'Снятие показаний',
            8, [10], EquipmentType::EQUIPMENT_HVS_COUNTER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Поверка',
            8, [11], EquipmentType::EQUIPMENT_HVS_COUNTER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена',
            8, [11], EquipmentType::EQUIPMENT_HVS_COUNTER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр изоляционного покрытия',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление изоляционного покрытия',
            8, [6, 7], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Общий осмотр технического состояния',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Контроль параметров',
            8, [9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка состояния КиП',
            8, [9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена элементов',
            8, [12], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Опрессовка системы',
            8, [6, 7], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка исправности и запорно-регулирующей аппаратуры',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Планово-предупредительный ремонт',
            8, [2], EquipmentType::EQUIPMENT_GVS_MAIN, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр изоляционного покрытия',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление изоляционного покрытия',
            8, [1, 2], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_GVS_TOWER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Проверка состояния насоса',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_GVS_PUMP, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [1, 2, 6, 7, 12], EquipmentType::EQUIPMENT_GVS_PUMP, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр изоляционного покрытия',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление изоляционного покрытия',
            8, [6, 7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Общий осмотр технического состояния',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Контроль параметров',
            8, [9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка состояния КиП',
            8, [9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена элементов',
            8, [12], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Опрессовка системы',
            8, [6, 7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Пуско-наладочные работы',
            8, [6, 7], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка исправности и запорно-регулирующей аппаратуры',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Планово-предупредительный ремонт',
            8, [2], EquipmentType::EQUIPMENT_HEAT_MAIN, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр изоляционного покрытия',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление изоляционного покрытия',
            8, [1, 2], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_HEAT_TOWER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр систем отопления подъезда',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_HEAT_RADIATOR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_HEAT_RADIATOR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_HEAT_RADIATOR, $oid);

        self::insertIntoTaskTemplateNew($db, 'Снятие показаний',
            8, [10], EquipmentType::EQUIPMENT_HEAT_COUNTER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Поверка',
            8, [11], EquipmentType::EQUIPMENT_HEAT_COUNTER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена',
            8, [11], EquipmentType::EQUIPMENT_HEAT_COUNTER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Окраска',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр и проверка кровли',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт и восстановление',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр парапета',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew($db, 'Очистка',
            8, [6], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена кровельного покрытия',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF, $oid);
        self::insertIntoTaskTemplateNew($db, 'Очистка от снежного покрова',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF, $oid);

        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр люка и запорных устройств',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт люка и запорных устройств',
            8, [1], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);

        self::insertIntoTaskTemplateNew($db, 'Уборка',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр конструкций, перегородок и перекрытий',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка заземления, оборудования крыши',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка температурного-влажностного режима',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Дератизация и дезинфекция',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка на соответствие пожарной безопасности',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка слуховых окон',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ROOF_ROOM, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ROOF_ENTRANCE, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр тех. состояния отмостки',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт отмостки',
            8, [1, 2], EquipmentType::EQUIPMENT_BASEMENT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр тех. состояния видимых частей конструкций',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление эксплуатационных свойств конструкций',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_BASEMENT, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр конструкций',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт конструкций',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Отделочные работы',
            8, [6, 7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Работы с указателями, нумерацией и инф.досками',
            8, [7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр наличия указателей, нумерации и инф.досками',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Теплоизоляционные работы',
            8, [6, 7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Гидроизоляционные работы',
            8, [6, 7], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр отделки фасада',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_WALL, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение критичных повреждений',
            8, [8], EquipmentType::EQUIPMENT_WALL, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_WALL_WATER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_WALL_WATER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Очистка',
            8, [6, 7], EquipmentType::EQUIPMENT_WALL_WATER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_WALL_WATER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр асфальтового покрытия',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт асфальтового покрытия',
            8, [1, 2], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр состояния детской площадки и газонов',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Озеленение',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущие работы по облагораживанию детской площадки',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Уборка снега, наледи и обработка пескосоляной смесью',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Уборка',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Прочие работы',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD, $oid);

        self::insertIntoTaskTemplateNew($db, 'Проверка состояния гидроизоляции систем водоотвода',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление состояния гидроизоляции систем водоотвода',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Прочистка ливневой канализации',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Очистка системы',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD_DRENAGE, $oid);

        self::insertIntoTaskTemplateNew($db, 'Очистка',
            8, [6, 7], EquipmentType::EQUIPMENT_YARD_TBO, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр оконных блоков',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена стекол и ремонт',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр двери и запорных устройств',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_DOOR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт двери и запорных устройств',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_ENTRANCE_DOOR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_DOOR, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр состояния загрузочных клапанов',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт',
            8, [1, 2], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка состояния стволов',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка состояния мусоросборника',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Срочная ликвидация засора',
            8, [8], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Общий осмотр системы',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр ограждений',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр несущих конструкций и перекрытий',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт ограждений',
            8, [1, 8], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт несущих конструкций и перекрытий',
            8, [1, 8], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Отделочные работы',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Работы по нумерации',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр состояния отделки стен',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр состояния отделки потолка',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр состояния отделки пола',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Уборка',
            8, [6], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Окраска косоуров и металлических элементов',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Дератизация и дезинфекция',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка на соответствие пожарной безопасности',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Периодический ремонт',
            8, [2], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_STAIRS, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр кабины и проверка связи',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка сроков эксплуатации',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр состояния дверей лифта',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр предмашинного отделения',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2], EquipmentType::EQUIPMENT_LIFT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Аварийный ремонт',
            8, [8], EquipmentType::EQUIPMENT_LIFT, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт',
            8, [1], EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр козырька',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт козырька',
            8, [1, 2], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр перил, крыльца и пандуса',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Уборка',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Уборка снега и наледи',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ENTRANCE_MAIN, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Срочная ликвидация засора',
            8, [8], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_SEWER_PIPE, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Срочная ликвидация засора',
            8, [8], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);
        self::insertIntoTaskTemplateNew($db, 'Техническое обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_SEWER_MAIN, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_SEWER_WELL, $oid);

        self::insertIntoTaskTemplateNew($db, 'Снятие показаний',
            8, [10], EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Поверка',
            8, [11], EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER, $oid);
        self::insertIntoTaskTemplateNew($db, 'Замена',
            8, [11], EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER, $oid);

        self::insertIntoTaskTemplateNew($db, 'Проверка запирающих устройств',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт запирающих устройств',
            8, [1], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка заземления',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка устройств защитного отключения',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);
        self::insertIntoTaskTemplateNew($db, 'Отключение',
            8, [6, 7, 8], EquipmentType::EQUIPMENT_ELECTRICITY_VRU, $oid);

        self::insertIntoTaskTemplateNew($db, 'Проверка запирающих устройств',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт запирающих устройств',
            8, [1], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);
        self::insertIntoTaskTemplateNew($db, 'Отключение',
            8, [6, 7, 8], EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 12], EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Очистка',
            8, [6, 7], EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT, $oid);

        self::insertIntoTaskTemplateNew($db, 'Осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 12], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT, $oid);
        self::insertIntoTaskTemplateNew($db, 'Очистка',
            8, [6, 7], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT, $oid);

        self::insertIntoTaskTemplateNew($db, 'Устранение аварийных повреждений',
            8, [8], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE, $oid);

        self::insertIntoTaskTemplateNew($db, 'Уборка',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка температурно-влажностного режима',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Осмотр конструкций, перегородок и перекрытий',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2, 8], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Дератизация и дезинфекция',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка на соответствие пожарной безопасности',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка загазованности',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_ROOM, $oid);

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущий ремонт',
            8, [1, 2], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Проверка ограждений',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Восстановление ограждений',
            8, [1, 2], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'Уборка',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
        self::insertIntoTaskTemplateNew($db, 'очистка от снежного покрова',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_WINDOWS, $oid);
    }

    /**
     * @param $oid
     * @param $db Connection
     * @throws Exception
     */
    public static function loadReferencesAll2($oid, $db)
    {
        if ($oid == Organization::ORG_SERVICE_UUID) {
            self::insertIntoEquipmentType($db, EquipmentType::EQUIPMENT_BASEMENT_DOOR,
                EquipmentSystem::EQUIPMENT_SYSTEM_TECHNO,
                'Входные двери подвала');
        }

        self::insertIntoTaskTemplateNew($db, 'Общий осмотр',
            8, [3, 4, 5, 9], EquipmentType::EQUIPMENT_BASEMENT_DOOR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Ремонт двери и запорных устройств',
            8, [1, 2], EquipmentType::EQUIPMENT_BASEMENT_DOOR, $oid);
        self::insertIntoTaskTemplateNew($db, 'Текущее обслуживание',
            8, [6, 7], EquipmentType::EQUIPMENT_BASEMENT_DOOR, $oid);
    }

    /**
     * @param $oid
     * @param $db Connection
     * @throws Exception
     */
    public static function loadRequestTypes($oid, $db)
    {
        self::insertIntoRequestType($db, 'Внеочередной осмотр при форс-мажорных обстоятельствах',
            24, TaskType::TASK_TYPE_NOT_PLANNED_CHECK, $oid);
        self::insertIntoRequestType($db, 'Устранение протечки кровли',
            24, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение повреждения системы организованного водоотвода',
            120, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение утраты связи отдельных кирпичей с кладкой наружных стен, угрожающей их выпадением',
            24, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение повреждения окон подъезда в летний период',
            72, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение повреждения окон подъезда в зимний период',
            24, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение повреждения заполнения входных дверей',
            24, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Отслоение штукатурки потолка или верхней части стены, угрожающее ее обрушению',
            120, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Нарушение связи наружной облицовки на фасадах со стенами',
            1, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение неисправности лифта',
            24, TaskType::TASK_TYPE_REPAIR, $oid);

        self::insertIntoRequestType($db, 'Локализация аварийных повреждений ХВС/ГВС',
            0.5, TaskType::TASK_TYPE_NOT_PLANNED_CHECK, $oid);
        self::insertIntoRequestType($db, 'Локализация аварийных повреждений внутридомовых систем отопления',
            0.5,TaskType::TASK_TYPE_NOT_PLANNED_CHECK, $oid);
        self::insertIntoRequestType($db,
            'Ликвидация засоров внутридомовой инженерной системы водоотведения',
            2, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Локализация аварийных повреждений электроснабжения',
            0.5, TaskType::TASK_TYPE_NOT_PLANNED_CHECK, $oid);
        self::insertIntoRequestType($db, 'Ликвидацию засоров мусоропроводов внутри многоквартирных',
            2, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение аварийных повреждений внутридомовых систем',
            72, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение аварийных повреждений внутридомовых систем отопления',
            72, TaskType::TASK_TYPE_REPAIR, $oid);
        self::insertIntoRequestType($db, 'Устранение аварийных повреждений внутридомовых систем  электроснабжения',
            72, TaskType::TASK_TYPE_REPAIR, $oid);
        // Особый тип без шаблона задачи
        $currentTime = date('Y-m-d\TH:i:s');
        $db->createCommand()->insert('request_type', [
            'uuid' => MainFunctions::GUID(),
            'oid' => $oid,
            'title' => 'Другой характер обращения',
            'taskTemplateUuid' => null,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ])->execute();

    }

    /**
     * @param $db Connection
     * @param $uuid
     * @param $title
     * @param $organizationUuid
     * @throws Exception
     */
//    private static function insertIntoHouseType($db, $uuid, $title, $organizationUuid)
//    {
//        $currentTime = date('Y-m-d\TH:i:s');
//        $db->createCommand()->insert('{{%house_type}}', [
//            'uuid' => $uuid,
//            'title' => $title,
//            'oid' => $organizationUuid,
//            'createdAt' => $currentTime,
//            'changedAt' => $currentTime
//        ])->execute();
//    }

    /**
     * @param $db Connection
     * @param $title
     * @param $description
     * @param $normative
     * @param $taskTypeUuid
     * @param $equipmentTypeUuid
     * @param $organizationUuid
     * @throws Exception
     */
    private static function insertIntoTaskTemplate($db, $title, $description, $normative, $taskTypeUuid, $equipmentTypeUuid,
                                                   $organizationUuid)
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $uuid = MainFunctions::GUID();
        $db->createCommand()->insert('task_template', [
            'uuid' => $uuid,
            'title' => $title,
            'description' => $description,
            'normative' => $normative,
            'taskTypeUuid' => $taskTypeUuid,
            'oid' => $organizationUuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ])->execute();

        $db->createCommand()->insert('task_template_equipment_type', [
            'uuid' => MainFunctions::GUID(),
            'equipmentTypeUuid' => $equipmentTypeUuid,
            'taskTemplateUuid' => $uuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ])->execute();
    }

    /**
     * @param $db Connection
     * @param $uuid
     * @param $equipmentSystemUuid
     * @param $title
     * @throws Exception
     */
    private static function insertIntoEquipmentType($db, $uuid, $equipmentSystemUuid, $title) {
        $currentTime = date('Y-m-d\TH:i:s');
        $db->createCommand()->insert('equipment_type', [
            'uuid' => $uuid,
            'title' => $title,
            'equipmentSystemUuid' => $equipmentSystemUuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ])->execute();
    }

    /**
     * @param $db Connection
     * @param $title
     * @param $normative
     * @param $taskTypes
     * @param $equipmentTypeUuid
     * @param $organizationUuid
     * @throws Exception
     */
    private static function insertIntoTaskTemplateNew($db, $title, $normative, $taskTypes, $equipmentTypeUuid, $organizationUuid)
    {
        $currentTime = date('Y-m-d\TH:i:s');

        foreach ($taskTypes as $taskType) {
            $uuid = MainFunctions::GUID();

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
                case 1:
                    $taskTypeUuid = TaskType::TASK_TYPE_CURRENT_REPAIR;
                    break;
                case 2:
                    $taskTypeUuid = TaskType::TASK_TYPE_PLAN_REPAIR;
                    break;
                case 3:
                    $taskTypeUuid = TaskType::TASK_TYPE_CURRENT_CHECK;
                    break;
                case 4:
                    $taskTypeUuid = TaskType::TASK_TYPE_NOT_PLANNED_CHECK;
                    break;
                case 5:
                    $taskTypeUuid = TaskType::TASK_TYPE_SEASON_CHECK;
                    break;
                case 6:
                    $taskTypeUuid = TaskType::TASK_TYPE_PLAN_TO;
                    break;
                case 7:
                    $taskTypeUuid = TaskType::TASK_TYPE_NOT_PLAN_TO;
                    break;
                case 8:
                    $taskTypeUuid = TaskType::TASK_TYPE_REPAIR;
                    break;
                case 9:
                    $taskTypeUuid = TaskType::TASK_TYPE_CONTROL;
                    break;
                case 10:
                    $taskTypeUuid = TaskType::TASK_TYPE_MEASURE;
                    break;
                case 11:
                    $taskTypeUuid = TaskType::TASK_TYPE_POVERKA;
                    break;
                case 12:
                    $taskTypeUuid = TaskType::TASK_TYPE_INSTALL;
                    break;
                default:
                    $taskTypeUuid = TaskType::TASK_TYPE_CURRENT_REPAIR;
            }

            $db->createCommand()->insert('task_template', [
                'uuid' => $uuid,
                'title' => $title,
                'description' => $title,
                'normative' => $normative,
                'taskTypeUuid' => $taskTypeUuid,
                'oid' => $organizationUuid,
                'createdAt' => $currentTime,
                'changedAt' => $currentTime
            ])->execute();

            $db->createCommand()->insert('task_template_equipment_type', [
                'uuid' => MainFunctions::GUID(),
                'equipmentTypeUuid' => $equipmentTypeUuid,
                'taskTemplateUuid' => $uuid,
                'createdAt' => $currentTime,
                'changedAt' => $currentTime
            ])->execute();
        }
    }

    /**
     * @param $db Connection
     * @param $title
     * @param $normative
     * @param $taskTypeUuid
     * @param $organizationUuid
     * @throws Exception
     */
    private static function insertIntoRequestType($db, $title, $normative, $taskTypeUuid, $organizationUuid) {
        $currentTime = date('Y-m-d\TH:i:s');
        $uuid = MainFunctions::GUID();
        $db->createCommand()->insert('task_template', [
            'uuid' => $uuid,
            'title' => $title,
            'description' => $title,
            'normative' => $normative,
            'oid' => $organizationUuid,
            'taskTypeUuid' => $taskTypeUuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ])->execute();

        $db->createCommand()->insert('request_type', [
            'uuid' => MainFunctions::GUID(),
            'oid' => $organizationUuid,
            'title' => $title,
            'taskTemplateUuid' => $uuid,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ])->execute();
    }

}

