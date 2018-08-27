<?php

use common\models\Task;
use common\models\TaskStatus;
use common\models\TaskVerdict;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $model */

$gridColumns = [
    [
        'attribute' => '_id',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (isset($data['taskTemplate'])) {
                return $data['taskTemplate']->title;
            } else {
                return 'неизвестно';
            }
        }
    ],
    [
        'attribute' => 'comment',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (isset($data['comment'])) {
                return $data['comment'];
            } else {
                return 'неизвестно';
            }
        }
    ],
    [
        'attribute' => 'taskStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $color = 'background-color: white';
            if ($model['taskStatusUuid'] == TaskStatus::CANCELED
                || $model['taskStatusUuid'] == TaskStatus::NEW_TASK
            ) {
                $color = 'background-color: gray';
            }

            if ($model['taskStatusUuid'] == TaskStatus::IN_WORK) {
                $color = 'background-color: yellow';
            }

            if ($model['taskStatusUuid'] == TaskStatus::UN_COMPLETE) {
                $color = 'background-color: lightred';
            }

            if ($model['taskStatusUuid'] == TaskStatus::COMPLETE) {
                $color = 'background-color: green';
            }

            return "<span class='badge' style='"
                . $color . "; height: 13px; margin-top: -3px'> </span>&nbsp;" .
                $model['taskStatus']->title;
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'taskVerdictUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $color = 'background-color: gray';
            if ($model['taskVerdictUuid'] == TaskVerdict::INSPECTED) {
                $color = 'background-color: green';
            }

            return "<span class='badge' style='"
                . $color . "; height: 13px; margin-top: -3px'> </span>&nbsp;" .
                $model['taskVerdict']->title;
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'startDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            return $data->startDate;
        }
    ],
    [
        'attribute' => 'endDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->endDate;
        }
    ],
];

$tasks = Task::find()->where(['orderUuid' => $model['uuid']]);
$provider = new ActiveDataProvider(
    [
        'query' => $tasks,
        'sort' => [
            'defaultOrder' => [
                'startDate' => SORT_DESC
            ]
        ],
    ]
);

echo GridView::widget(
    [
        'dataProvider' => $provider,
        'columns' => $gridColumns,
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'containerOptions' => ['style' => 'overflow: auto'],
        'beforeHeader' => [
            '{toggleData}'
        ],
        'toolbar' => [
            ['content' =>
                Html::a(
                    'Новая',
                    [
                        '/task/create',
                        'from' => 'orders/table',
                        'order' => $model['uuid']
                    ],
                    ['class' => 'btn btn-success']
                )
            ]
        ],
        'pjax' => true,
        'showPageSummary' => false,
        'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
        'summary' => '',
        'bordered' => true,
        'striped' => false,
        'condensed' => true,
        'responsive' => false,
        'hover' => true,
        'floatHeader' => false,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<i class="glyphicon glyphicons-spade"></i>&nbsp; Задачи',
            'headingOptions' => ['style' => 'background: #337ab7']
        ],
    ]
);
