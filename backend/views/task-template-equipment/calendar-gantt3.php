<?php

$this->title = 'Календарь задач по обслуживанию';

/* @var $events
 * @var $events2
 */

use professionalweb\ScheduleWidget\ScheduleWidget;
use yii\web\JsExpression;
?>

    <div class="col-md-12">
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><a href="" data-toggle="collapse">План-график работ</a></h4>
                </div>
                <div class="panel-collapse collapse am-collapse in">
                    <div class="panel-body" style="position: relative;">
<?php
echo ScheduleWidget::widget([
    'clientOptions' => [
        'columnLimitThreshold' => '20',
        'headers-formats' => [
            'year' => 'YYYY',
            'month' => 'MM',
        ],
        'view-scale' => 'day',
        'auto-expand' => 'both',
        'headers' => [
            'month', 'week'
        ]
    ],
    'plugins' => [
        ScheduleWidget::PLUGIN_MOVABLE => [],
        ScheduleWidget::DRAW_TASK => [
            'enabled' => 'true'
        ],
        ScheduleWidget::PLUGIN_RESIZE_SENSOR => [],
        ScheduleWidget::PLUGIN_BOUNDS => [
            'enabled' => 'true'
        ],
        ScheduleWidget::PLUGIN_TABLE => [
            'headers' => [
                'model.name' => 'Оборудование',
                'from' => 'Начало',
                'to' => 'Окончание'
            ]
        ],
        ScheduleWidget::PLUGIN_TOOLTIP => []
    ],
    'events' => [
        ScheduleWidget::EVENT_TASK_MOVEEND => new JsExpression('function(task){'
            . 'console.log(task.row.model);'
            . '}'),
        ScheduleWidget::EVENT_TASK_MOVEBEGIN => new JsExpression('function(task){'
            . 'console.log("start");'
            . '}'),
        ScheduleWidget::EVENT_TASK_MOVE => new JsExpression('function(task, fromRow){'
            . 'console.log(task.row.model);'
            . '}')
    ],
    'data' => json_encode($events)
]);
?>
                    </div>
                </div>
            </div>
        </div>
    </div>

