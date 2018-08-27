<?php
/* @var $searchModel backend\models\EventSearch */

use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Html;
//use kartik\widgets\ActiveForm;

$this->title = Yii::t('app', 'ТОИРУС::Типы аттрибутов');

$types = [
    '1' => 'Файл',
    '2' => 'Строка',
    '3' => 'Значение'];

$refresh = [
    '0' => 'Нет',
    '1' => 'Обновляемый'];

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'name',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'asPopover' => false,
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'type',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'data' => $types,
            'hideSearch' => true,
            'pluginOptions'=>[
                'placeholder' => 'Тип',
                'multiple' => false,
            ],
        ],
        'editableOptions'=> [
            'asPopover' => false,
            'displayValueConfig'=> $types,
            'inputType' => Editable::INPUT_SELECT2,
            'options' => [
                'data' => $types,
                'hideSearch' => true
            ],
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'units',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
        ],
        'editableOptions' => [
            'asPopover' => false
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->units;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'refresh',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
        ],
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'data' => $refresh,
            'hideSearch' => true,
            'pluginOptions'=>[
                'placeholder' => 'Тип',
                'multiple' => false
            ],
        ],
        'editableOptions'=> [
            'asPopover' => false,
            'displayValueConfig'=> $refresh,
            'inputType' => Editable::INPUT_SELECT2,
            'options' => [
                'data' => $refresh,
                'hideSearch' => true
            ],
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->refresh;
        }
    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('Новый', ['/event/create'], ['class'=>'btn btn-success']),
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}',
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'event'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => false,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; Типы аттрибутов',
        'headingOptions' => ['style' => 'background: #337ab7']

    ],
]);
