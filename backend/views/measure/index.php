<?php
/* @var $searchModel backend\models\MeasureSearch */

use common\models\MeasureType;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Отчет о показаниях приборов');

$gridColumns = [
    [
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'header' => 'Оборудование',
        'mergeHeader' => true,
        'content' => function ($data) {
            return $data['equipment']['title'];
        }
    ],
    [
        'attribute' => 'equipment.serial',
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
            return $data['equipment']['object']->getFullTitle();
        }
    ],
    [
        'attribute' => 'date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'value',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return "<span class='badge' style='background-color: green; height: 22px; margin-top: -3px'>".$data['value']."</span>";
        }
    ],
    [
        'attribute' => 'measureType.title',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Тип измерения',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return "<span class='badge' style='background-color: blue; height: 22px; margin-top: -3px'>".
                $data['measureType']['title']."</span>";
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'header' => 'Действия',
        'template' => '{delete}'
    ]
];

$measureType = MeasureType::find()->all();
$items = ArrayHelper::map($measureType, 'uuid', 'title');
$start_date = '2018-12-31';
$end_date = '2021-12-31';
$type = '';
if (isset($_GET['type']))
    $type = $_GET['type'];
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];

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
            '<form action="/measure/index"><div class="row" style="margin-bottom: 8px; width:100%">
             <div class="col-sm-4" style="width:30%">' .
            Select2::widget([
                'name' => 'type',
                'language' => 'ru',
                'data' => $items,
                'value' => $type,
                'options' => ['placeholder' => 'Тип измерений'],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]) . '</div><div class="col-sm-4" style="width:25%">' .
            DatePicker::widget([
                'name' => 'start_time',
                'removeButton' => false,
                'value' => $start_date,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) . '</div><div class="col-sm-4" style="width:25%">' .
            DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
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
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Отчет о показаниях приборов',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);