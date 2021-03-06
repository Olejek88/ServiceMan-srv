<?php
/* @var $searchModel backend\models\ObjectsSearch */

use common\models\Objects;
use common\models\ObjectType;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Объекты');

$value = '';
if (isset($_GET['address']))
    $value = $_GET['address'];

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('object-details', ['model' => $model]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'fullTitle',
        'vAlign' => 'middle',
        'value' => function ($data) {
            /** @var Objects $data */
            return $data->getFullTitle();
        },
        'header' => 'Дом',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'width' => '180px',
        'header' => 'Объект',
        'format' => 'raw',
        'value' => function ($data) {
            return $data['objectType']['title'] . ' ' . $data['title'];
        }
    ],
    /*    [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'objectStatusUuid',
            'header' => 'Статус',
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'width' => '180px',
            'value' => function ($model, $key, $index, $widget) {
                $color = 'background-color: yellow';
                if ($model['objectStatusUuid'] == ObjectStatus::OBJECT_STATUS_DEFAULT)
                    $color = 'background-color: gray';
                if ($model['objectStatusUuid'] == ObjectStatus::OBJECT_STATUS_NO_ENTRANCE ||
                    ObjectStatus::OBJECT_STATUS_NO_ENTRANCE)
                    $color = 'background-color: lightred';
                if ($model['objectStatusUuid'] == ObjectStatus::OBJECT_STATUS_OK)
                    $color = 'background-color: green';
                return "<span class='badge' style='" . $color . "; height: 12px; margin-top: -3px'> </span>&nbsp;
                            " . $model['objectStatus']->title;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(ObjectStatus::find()->orderBy('title')->all(),
                'uuid', 'title'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Любой'],
            'format' => 'raw'
        ],*/
    [
        'attribute' => 'objectTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип объекта',
        'filter' => ArrayHelper::map(ObjectType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'value' => 'objectType.title',
        'contentOptions' => [
            'class' => 'table_class'
        ]
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'width' => '180px',
        'attribute' => 'square',
        'mergeHeader' => true,
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'header' => 'Площадь',
    ],

    /*    [
        'class' => 'kartik\grid\DataColumn',
        'width' => '180px',
        'attribute' => 'changedAt',
        'mergeHeader' => true,
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'header' => 'Дата изменения',
    ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'template' => '{update} {delete}'
    ]
];

echo GridView::widget([
    'id' => 'object-table',
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
            Html::a('Новый', ['/object/create'], ['class' => 'btn btn-success'])
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
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Объекты',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
