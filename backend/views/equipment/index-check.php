<?php
/* @var $searchModel backend\models\EquipmentSearch */

use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Отчеты по поверкам приборов');

$gridColumns = [
    [
        'attribute' => 'title',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Оборудование',
        'mergeHeader' => true,
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'attribute' => 'serial',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Серийный номер',
        'mergeHeader' => true,
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Адрес',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            return $data['object']->getFullTitle();
        }
    ],
    [
        'attribute' => 'testDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if ($data['testDate']) {
                $testDate = strtotime($data['testDate']);
                return "<span class='badge' style='background-color: darkblue; height: 22px; margin-top: -3px'>" .
                    date('Y-m-d', $testDate) . "</span>";
            }
            return "<span class='badge' style='background-color: gray; height: 22px; margin-top: -3px'>не указана</span>";
        }
    ],
    [
        'attribute' => 'nextDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if ($data['nextDate']) {
                $nextDate = strtotime($data['nextDate']);
                return "<span class='badge' style='background-color: darkblue; height: 22px; margin-top: -3px'>" .
                    date('Y-m-d', $nextDate) . "</span>";
            }
            return "<span class='badge' style='background-color: gray; height: 22px; margin-top: -3px'>не указана</span>";
        }
    ],
    [
        'attribute' => 'replaceDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if ($data['replaceDate']) {
                $replaceDate = strtotime($data['replaceDate']);
                return "<span class='badge' style='background-color: yellow; height: 22px; margin-top: -3px'>" .
                    date('Y-m-d', $replaceDate) . "</span>";
            }
            return "<span class='badge' style='background-color: gray; height: 22px; margin-top: -3px'>не указана</span>";
        }
    ]
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px important!'],
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action="/equipment/index-check"><div class="row" style="margin-bottom: 8px; width:100%">
             <div class="col-sm-4" style="width:25%">' .
            DatePicker::widget([
                'name' => 'start_time',
                'value' => '2018-12-01',
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) . '</div><div class="col-sm-4" style="width:25%">' .
            DatePicker::widget([
                'name' => 'end_time',
                'value' => '2021-12-31',
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ]
            ]) . '</div><div class="col-sm-2" style="width:12%">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']) . '</div>' .
            '<div class="col-sm-1" style="width:8%">' . '{export}' . '</div></div></form>',
            'options' => ['style' => 'width:100%']
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'orders'
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
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Отчет о поверках приборов',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);