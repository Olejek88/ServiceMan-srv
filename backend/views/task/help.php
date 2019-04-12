<?php

$this->title = Yii::t('app', 'Карта действий');

/* @var int $actionTypeCount */

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');

        // For each orgchart box, provide the name, manager, and tooltip to show.
        data.addRows([
            [{v:'Start', f:'<b>Начало работы</b>'},
                '', 'Начинаем работу с системой ТОиРУС'],
            [{v:'Users', f:'<b>Добавляем пользователей</b>'},
                'Start', 'Добавляем пользователей и клиентов'],
            [{v:'AddUsers', f:'<a href="/user/create">Добавляем операторов</a>'},
                'Users', 'Заполняем таблицу операторов'],
            [{v:'AddUser', f:'<a href="/users/create">Добавляем пользователей</a>'},
                'AddUsers', 'Заполняем таблицу пользователей'],
            [{v:'UserChannels', f:'<a href="/user-channel">Каналы оповещений</a>'},
                'AddUser', 'Заполняем таблицу каналов'],

            [{v:'AddRefs', f:'<b>Заполняем справочники</b>'},
                'Start', 'Заполняем справочники'],
            [{v:'AddCritical', f:'<a href="/critical-type/create">Добавляем уровни критичности</a>'},
                'AddRefs', 'Добавляем уровни критичности'],
            [{v:'AddDefectType', f:'<a href="/defect-type/create">Добавляем типы дефектов</a>'},
                'AddCritical', 'Добавляем типы дефектов'],
            [{v:'MeasureType', f:'<a href="/measure-type/create">Типы измерений</a>'},
                'AddDefectType', 'Типы измерений'],
            [{v:'DocumentationType', f:'<a href="/documentation-type/create">Типы документации</a>'},
                'MeasureType', 'Типы документации оборудования'],
            [{v:'EquipmentRegisterType', f:'<a href="/equipment-register-type/create">Типы записей журнала оборудования</a>'},
                'DocumentationType', 'Типы записей журнала оборудования'],
            [{v:'ActionType', f:'<a href="/action-type/create">Типы реакций [<?php echo $actionTypeCount ?>]</a>'},
                'EquipmentRegisterType', 'Типы реакций на события'],

            [{v:'Object', f:'<b>Добавляем объекты</b>'},
                'Start', 'Конфигурируем объекты'],
            [{v:'ObjectType', f:'<a href="/object-type/create">Конфигурируем типы объектов</a>'},
                'Object', 'Конфигурируем типы объектов'],
            [{v:'Objects', f:'<a href="/object-type/create">Добавляем объекты</a>'},
                'ObjectType', 'Добавление объектов'],

            [{v:'Equipment', f:'<b>Заносим оборудование</b>'},
                'Start', 'Добавляем типы оборудования'],
            [{v:'AddEquipmentType', f:'<a href="/equipment-type/create">Добавляем типы оборудования</a>'},
                'Equipment', 'Добавляем типы оборудования'],
            [{v:'AddEquipmentModel', f:'<a href="/equipment-model/create">Добавляем модели оборудования</a>'},
                'AddEquipmentType', 'Добавляем модели оборудования'],
            [{v:'AddEquipmentStatus', f:'<a href="/equipment-status/create">Статусы оборудования</a>'},
                'AddEquipmentModel', 'Добавляем оборудование'],
            [{v:'AddEquipment', f:'<a href="/equipment-type/create">Добавляем оборудование</a>'},
                'AddEquipmentStatus', 'Добавляем оборудование'],
            [{v:'AddDocumentation', f:'<a href="/documentation/create">Добавляем документацию</a>'},
                'AddEquipment', 'Добавляем документацию'],

            [{v:'ToolsAndParts', f:'<b>Запчасти и инструменты</b>'},
                'Start', 'Добавляем ЗиП и инструменты'],
            [{v:'AddPartType', f:'<a href="/repair-part-type/create">Типы запчастей</a>'},
                'ToolsAndParts', 'Добавляем типы ЗиП'],
            [{v:'AddPart', f:'<a href="/repair-part/create">Запчасти</a>'},
                'AddPartType', 'Добавляем ЗиП'],
            [{v:'AddToolType', f:'<a href="/tool-type/create">Типы инструментов</a>'},
                'ToolsAndParts', 'Добавляем типы инструментов'],
            [{v:'AddTool', f:'<a href="/tool/create">Инструменты</a>'},
                'AddToolType', 'Добавляем инструменты'],

            [{v:'AddTasks', f:'<b>Конфигурируем задачи</b>'},
                'Start', 'Конфигурируем задачи'],

            [{v:'Operations', f:'<b>Операции</b>'},
                'AddTasks', 'Добавляем задачи'],
            [{v:'AddOperationType', f:'<a href="/operation-type/create">Добавляем типы</a>'},
                'Operations', 'Добавляем типы операций'],
            [{v:'AddOperationVerdict', f:'<a href="/operation-verdict/create">Добавляем вердикты</a>'},
                'AddOperationType', 'Добавляем вердикты операций'],
            [{v:'AddOperationStatus', f:'<a href="/operation-status/create">Добавляем статусы</a>'},
                'AddOperationVerdict', 'Добавляем статусы операций'],

            [{v:'AddOperationTemplate', f:'<a href="/operation-template/create">Добавляем шаблон операции</a>'},
                'AddOperationStatus', 'Добавляем шаблон операции'],
            [{v:'AddOperationTools', f:'<a href="/operation-tool/tree">Добавляем инструменты к шаблону</a>'},
                'AddOperationTemplate', 'Добавляем инструменты'],
            [{v:'AddOperationParts', f:'<a href="/operation-tool/tree">Добавляем ЗиП к шаблону</a>'},
                'AddOperationTemplate', 'Добавляем ЗиП'],

            [{v:'Tasks', f:'<b>Задачи</b>'},
                'AddTasks', 'Добавляем задачи'],
            [{v:'AddTaskType', f:'<a href="/task-type/create">Добавляем типы</a>'},
                'Tasks', 'Добавляем типы задач'],
            [{v:'AddTaskVerdict', f:'<a href="/task-verdict/create">Добавляем вердикты</a>'},
                'AddTaskType', 'Добавляем вердикты задач'],
            [{v:'AddTaskStatus', f:'<a href="/task-status/create">Добавляем статусы</a>'},
                'AddTaskVerdict', 'Добавляем статусы задач'],

            [{v:'Stages', f:'<b>Этапы</b>'},
                'AddTasks', 'Добавляем этапы задач'],
            [{v:'AddStageType', f:'<a href="/stage-type/create">Добавляем типы</a>'},
                'Stages', 'Добавляем типы этапов'],
            [{v:'AddStageVerdict', f:'<a href="/stage-verdict/create">Добавляем вердикты</a>'},
                'AddStageType', 'Добавляем вердикты этапов'],
            [{v:'AddStageStatus', f:'<a href="/stage-status/create">Добавляем статусы</a>'},
                'AddStageVerdict', 'Добавляем статусы этапов'],


            [{v:'Orders', f:'<b>Наряды</b>'},
                'AddTasks', 'Конфигурируем наряды'],
            [{v:'AddOrderVerdict', f:'<a href="/order-verdict/create">Добавляем вердикты</a>'},
                'Orders', 'Добавляем вердикты нарядов'],
            [{v:'AddOrderStatus', f:'<a href="/order-status/create">Добавляем статусы</a>'},
                'AddOrderVerdict', 'Добавляем статусы нарядов'],
            [{v:'AddOrderLevel', f:'<a href="/order-level/create">Добавляем уровни</a>'},
                'AddOrderStatus', 'Добавляем уровни нарядов'],
            [{v:'AddOperation', f:'<a href="/operation/create">Добавляем операции</a>'},
                'AddOrderLevel', 'Добавляем операции'],
            [{v:'AddStage', f:'<a href="/stage/create">Добавляем этапы</a>'},
                'AddOperation', 'Добавляем этапы'],
            [{v:'AddTask', f:'<a href="/task/create">Добавляем задачи</a>'},
                'AddStage', 'Добавляем задачи'],
            [{v:'AddOrder', f:'<a href="/orders/create">Формируем наряд</a>'},
                'AddTask', 'Добавляем наряды'],

            [{v:'AddStageTemplate', f:'<a href="/stage-template/create">Добавляем шаблон этапа</a>'},
                'AddStageStatus', 'Добавляем шаблон этапа'],
            [{v:'AddStageOperation', f:'<a href="/stage-operation/tree">Связываем шаблон этапа с операциями</a>'},
                'AddStageTemplate', 'Связываем шаблон с операциями'],
            [{v:'AddEquipmentStage', f:'<a href="/equipment-stage/tree">Связываем шаблон этапа с оборудованием</a>'},
                'AddStageOperation', 'Связываем шаблон с оборудованием'],
            [{v:'AddTaskEquipmentStage', f:'<a href="/task-equipment-stage/tree">Связываем шаблон задачи с шаблонами этапов</a>'},
                'AddEquipmentStage', 'Связываем шаблон задачи с шаблонами этапов'],

            [{v:'AddTaskStatus', f:'<a href="/task-type/create">Добавляем </a>'},
                'AddTaskVerdict', 'Добавляем статусы задач'],

            [{v:'AddTaskTemplate', f:'<a href="/task-template/create">Добавляем шаблон задачи</a>'},
                'AddTaskStatus', 'Добавляем шаблон задачи'],
            [{v:'AddTaskEquipmentStage', f:'<a href="/task-equipment-stage/tree">Связываем шаблон задачи с шаблонами этапов</a>'},
                'AddEquipmentStage', 'Связываем шаблон задачи с шаблонами этапов'],

            [{v:'SCADA', f:'<b>Конфигурируем взаимодействие</b>'},
                'Start', 'SCADA'],
            [{v:'scadaRefs', f:'<b>Базовые справочники</b>'},
                'SCADA', 'SCADA'],
            [{v:'MessageType', f:'<a href="/message-type/create">Типы сообщений</a>'},
                'scadaRefs', 'Типы сообщений'],
            [{v:'MessageChannel', f:'<a href="/message-channel/create">Каналы сообщений</a>'},
                'MessageType', 'Каналы сообщений'],
            [{v:'ActionType', f:'<a href="/action-type/create">Типы действий</a>'},
                'MessageChannel', 'Типы действий'],

            [{v:'scadaEvents', f:'<b>События и системы</b>'},
                'SCADA', 'События и системы'],
            [{v:'ExternalSystem', f:'<a href="/external-system/create">Внешние системы</a>'},
                'scadaEvents', 'Внешние системы'],
            [{v:'ExternalTag', f:'<a href="/external-tag/create">Тег события</a>'},
                'ExternalSystem', 'Теги внешних системы'],
            [{v:'ExternalEvent', f:'<a href="/external-event">Внешние события</a>'},
                'ExternalTag', 'События внешних систем'],

            [{v:'TSO', f:'<b>ТСО</b>'},
                'Start', 'ТСО'],
            [{v:'AttributeType', f:'<a href="/attribute-type/create">Типы аттрибутов</a>'},
                'TSO', 'Типы аттрибутов'],
            [{v:'Event', f:'<a href="/event/create">События</a>'},
                'AttributeType', 'Каналы сообщений'],
            [{v:'EventAttributeType', f:'<a href="/event-attribute-type/create">События и типы аттрибутов</a>'},
                'Event', 'Типы действий'],
            [{v:'AttributesObjects', f:'<a href="/objects-attribute/create">Аттрибуты объектов</a>'},
                'EventAttributeType', 'Аттрибуты объектов'],
            [{v:'AttributesEquipment', f:'<a href="/equipment-attribute/create">Аттрибуты оборудования</a>'},
                'AttributesObjects', 'Аттрибуты оборудования'],
            [{v:'AttributesUser', f:'<a href="/users-attribute/create">Аттрибуты пользователей</a>'},
                'AttributesEquipment', 'Аттрибуты пользователей'],
        ]);
        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(data, {allowHtml:true});
    }
</script>

<div class="orders-index">
    <div id="chart_div"></div>
</div>
