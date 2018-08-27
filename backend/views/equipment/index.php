<?php
/* @var $searchModel backend\models\EquipmentSearch */

use common\models\EquipmentModel;
use common\models\EquipmentStatus;
use common\models\Objects;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Оборудование');

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
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('equipment-details', ['model' => $model]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
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
        'attribute' => 'equipmentModelUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(EquipmentModel::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'header' => 'Модель оборудования '.Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/equipment-model/create?from=equipment/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => 'equipmentModel.title',
        'editableOptions'=> function ($model, $key, $index, $widget) {
            $models = ArrayHelper::map(EquipmentModel::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'header' => 'Модель оборудования',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'inventoryNumber',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->inventoryNumber;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'locationUuid',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => 'location.title',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Местоположение '.Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/objects/create?from=equipment/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'filter' => ArrayHelper::map(Objects::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'editableOptions'=> function ($model, $key, $index, $widget) {
            $models = ArrayHelper::map(Objects::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'header' => 'Локация',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'equipmentStatusUuid',
        'header' => 'Статус '.Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/equipment-status/create?from=equipment/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'vAlign' => 'middle',
        'width' => '180px',
        'editableOptions'=> function () {
            $status=[];
            $list=[];
            $statuses = EquipmentStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color='background-color: white';
                if ($stat['uuid']==EquipmentStatus::UNKNOWN ||
                    $stat['uuid']==EquipmentStatus::NOT_MOUNTED)
                    $color='background-color: gray';
                if ($stat['uuid']==EquipmentStatus::NEED_CHECK ||
                    $stat['uuid']==EquipmentStatus::NEED_REPAIR)
                    $color='background-color: lightred';
                if ($stat['uuid']==EquipmentStatus::WORK)
                    $color='background-color: green';
                $list[$stat['uuid']] = $stat['title'];
                $status[$stat['uuid']] = "<span class='badge' style='".$color."; height: 12px; margin-top: -3px'> </span>&nbsp;".
                    $stat['title'];
            }
            return [
                'header' => 'Статус',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(EquipmentStatus::find()->orderBy('title')->all(),
                'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw'
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'equipment-table',
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
            Html::a('Новое', ['/equipment/create'], ['class'=>'btn btn-success']),
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Оборудование',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
