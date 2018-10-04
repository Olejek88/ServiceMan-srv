<?php
/* @var $searchModel backend\models\FlatSearch */

use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Flat;
use common\models\FlatStatus;
use common\models\FlatType;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Квартиры');

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
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
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'house',
        'vAlign' => 'middle',
        'width' => '220px',
        'value' => function ($data) {
            return 'ул.'.$data['house']['street']->title.', д.'.$data['house']->number;
        },
        'header' => 'Адрес',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'number',
        'vAlign' => 'middle',
        'width' => '180px',
        'header' => 'Квартира',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'flatStatusUuid',
        'header' => 'Статус',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => function ($model, $key, $index, $widget) {
                $color='background-color: yellow';
                if ($model['flatStatusUuid']==FlatStatus::FLAT_STATUS_DEFAULT)
                    $color='background-color: gray';
                if ($model['flatStatusUuid']==FlatStatus::FLAT_STATUS_NO_ENTRANCE ||
                    FlatStatus::FLAT_STATUS_ABSENT)
                    $color='background-color: lightred';
                if ($model['flatStatusUuid']==FlatStatus::FLAT_STATUS_OK)
                    $color='background-color: green';
                return "<span class='badge' style='".$color."; height: 12px; margin-top: -3px'> </span>&nbsp;  
                        " . $model['flatStatus']->title;
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(FlatStatus::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw'
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'flatTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип',
        'filter' => ArrayHelper::map(FlatType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions'=> function ($model, $key, $index, $widget) {
            $models = ArrayHelper::map(FlatType::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'header' => 'Тип квартиры',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'header' => 'Дата изменения',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'flat-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('Новая', ['/flat/create'], ['class'=>'btn btn-success']),
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'equipments'
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Квартира',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
