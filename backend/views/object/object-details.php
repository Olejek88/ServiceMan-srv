<?php

use common\models\ObjectContragent;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $model */

$gridColumns = [
    [
        'attribute' => '_id',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Название',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['title'];
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Тип контрагента',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['contragentType']['title'];
        }
    ],
    [
        'attribute' => 'address',
        'header' => 'Адрес',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['address'];
        }
    ],
    [
        'attribute' => 'phone',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Телефон',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['phone'];
        }
    ],
    [
        'attribute' => 'inn',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'ИНН',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['inn'];
        }
    ],
    [
        'attribute' => 'director',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Директор',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['director'];
        }
    ],
    [
        'attribute' => 'email',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Е-мэйл',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['contragent']['email'];
        }
    ],
    [
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Изменен',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data['contragent']['changedAt']) > 0)
                return date("d-m-Y h:m", strtotime($data['contragent']['changedAt']));
            else
                return 'не открыт';
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'header' => 'Действия',
        'buttons' => [
            'delete2' => function ($url,$model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-trash"></span>',
                    'object-contragent/delete?id='.$model["_id"]);
            },
        ],
        'template' => '{delete2}'
    ]
];

$objectContragents = ObjectContragent::find()->where(['objectUuid' => $model['uuid']]);
$provider = new ActiveDataProvider(
    [
        'query' => $objectContragents,
        'sort' =>false,
    ]
);

echo GridView::widget([
    'id' => 'flat-table',
    'dataProvider' => $provider,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('Новый', ['/contragent/create'], ['class' => 'btn btn-success'])
        ],
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Контрагенты',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
