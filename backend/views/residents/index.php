<?php
/* @var $searchModel backend\models\ResidentSearch */

use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Абоненты');

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
            return $data->id;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'owner',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->owner;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'inn',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->inn;
        }
    ],
    [
        'attribute'=>'flatUuid',
        'contentOptions' =>[
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'value'     => 'flatUuid',
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Абоненты',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
