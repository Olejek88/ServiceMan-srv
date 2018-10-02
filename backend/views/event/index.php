<?php
/* @var $searchModel backend\models\EventSearch */
/* @var $warnings string[] */

use kartik\grid\GridView;
use yii\helpers\Html;

//use kartik\widgets\ActiveForm;

$this->title = Yii::t('app', 'ТОИРУС::События по обслуживанию системы');

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'name',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'period',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'padding: 5px 10px 5px 10px;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->period;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'next_date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'filterType' => GridView::FILTER_DATETIME,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'editableOptions' => [
            'header' => 'Дата события',
            'size' => 'md',
            'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
    [
        'attribute' => 'last_date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->last_date;
        }
    ],
    [
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'active',
        'vAlign'=>'middle',
    ],
];

foreach ($warnings as $warning) {
    if ($warning!='') {
        echo '<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-ban"></i> Внимание!</h4>';
        echo $warning;
        echo '</div>';
    }
}
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
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; Запланированные события',
        'headingOptions' => ['style' => 'background: #337ab7']

    ],
]);
