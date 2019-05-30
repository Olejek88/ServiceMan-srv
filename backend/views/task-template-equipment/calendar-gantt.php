<?php

$this->title = 'Календарь задач по обслуживанию';

/* @var $events */

use professionalweb\ScheduleWidget\ScheduleWidget;
use yii\web\JsExpression;

echo ScheduleWidget::widget([
    'clientOptions' => [
        'daily' => 'true',
        'column-magnet' => 'column',
        'time-frames-magnet' => false
    ],
    'plugins' => [
        ScheduleWidget::PLUGIN_MOVABLE => [],
        ScheduleWidget::PLUGIN_TABLE => [
            'headers' => [
                'model.name' => 'Name'
            ]
        ],
        ScheduleWidget::PLUGIN_TOOLTIP => []
    ],
    'events' => [
        ScheduleWidget::EVENT_TASK_MOVEEND => new JsExpression('function(task){'
            .'console.log(task.row.model);'
            .'}')
    ],
    'data' => '[
        {"name":"Row №1","sortable":"false","tasks":[]},
        {"name":"Row №2","sortable":"false","tasks":[]},
        {"name":"Row №3","sortable":"false","tasks":[
            {"name":"Task №1","from":"2015 04 16","to":"2015 04 23"}
          ]
        }
      ]'
]);

