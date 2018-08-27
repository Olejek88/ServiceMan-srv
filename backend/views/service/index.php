<?php
/* @var $searchModel backend\models\ServiceSearch */

use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Сервисы');

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 30px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->title;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'service_name',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->service_name;
        }
    ],
    [
        'attribute' => 'status',
        'value' => function ($model) {
            if ($model->status==0)
                return "<span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span>";
            else
                return "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>";
        },
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'format' => 'raw'
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'delay',
        'vAlign' => 'middle',
        'hAlign' => 'center',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'active',
        'header' => 'Активность',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'vAlign' => 'middle',
        'width' => '70px',
        'value' => function ($model) {
            if ($model->active==0)
                return "<span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span> Не активен";
            else
                return "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span> Активен";
        },
        'editableOptions'=> function () {
            $status=[
                '0' => "<span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span>&nbsp;Не активен",
                '1' => "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>&nbsp;Активен"
            ];
            $list=['0' => 'Не активен',
                '1' => 'Активен'
            ];
            return [
                'header' => 'Статус',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'last_start_date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'attribute' => 'last_stop_date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'attribute' => 'last_message',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($model) {
            if ($model->last_message_type==0)
                return "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>&nbsp;".
                    $model->last_message;
            if ($model->last_message_type==1)
                return "<span class='badge' style='background-color: yellow; height: 12px; margin-top: -3px'> </span>&nbsp;".
                    $model->last_message;
            if ($model->last_message_type==2)
                return "<span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span>&nbsp;".
                    $model->last_message;
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'service-table',
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'],
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('Новый', ['/service/create'], ['class'=>'btn btn-success']),
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}',
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Сервисы',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
