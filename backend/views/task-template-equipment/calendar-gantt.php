<?php

$this->title = 'Календарь задач по обслуживанию';

/* @var $events */

use professionalweb\ScheduleWidget\ScheduleWidget;
use yii\web\JsExpression;

echo ScheduleWidget::widget([
    'clientOptions' => [
        'daily' => 'true',
        'auto-expand' => 'both',
        'timespans' => [
              'from' => '2019-05-20 00:00:00',
              'to' => '2019-06-20 00:00:00'
        ],
        'columnLimitThreshold' => '20',
        'headersFormats' => [
            'month' => 'MMMM'
        ]
    ],
    'plugins' => [
        ScheduleWidget::PLUGIN_MOVABLE => [
            'allow-moving' => true,
            'enabled' => true,
            'allowMoving' => true
        ],
        ScheduleWidget::PLUGIN_TABLE => [
            'headers' => [
                'model.name' => 'Оборудование'
            ]
        ],
        ScheduleWidget::PLUGIN_TOOLTIP => []
    ],
    'events' => [
        ScheduleWidget::EVENT_TASK_MOVEEND => new JsExpression('function(task){'
            .'console.log(task.row.model);'
            .'}')
    ],
    'data' => json_encode($events)
]);

