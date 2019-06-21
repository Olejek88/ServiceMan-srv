<?php
/* @var $searchModel backend\models\EquipmentSearch */

use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\UserSystem;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Оборудование');

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
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'header' => 'Оборудование',
        'mergeHeader' => true,
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'objectUuid',
        'vAlign' => 'middle',
        'width' => '280px',
        'mergeHeader' => true,
        'value' => function ($data) {
            return $data['object']['house']['street']->title . ', ' . $data['object']['house']->number . ' - ' . $data['object']->title;
        },
        'header' => 'Объект ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/object/create?from=equipment/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'equipmentTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => 'equipmentType.title',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип оборудования ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/equipment-type/create?from=equipment/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'filter' => ArrayHelper::map(EquipmentType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => function ($model, $key, $index, $widget) {
            $models = ArrayHelper::map(EquipmentType::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'header' => 'Тип оборудования',
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
        'header' => 'Статус ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/equipment-status/create?from=equipment/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'editableOptions' => function () {
            $status = [];
            $list = [];
            $statuses = EquipmentStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color = 'background-color: white';
                if ($stat['uuid'] == EquipmentStatus::UNKNOWN ||
                    $stat['uuid'] == EquipmentStatus::NOT_MOUNTED)
                    $color = 'background-color: gray';
                if ($stat['uuid'] == EquipmentStatus::NOT_WORK)
                    $color = 'background-color: lightred';
                if ($stat['uuid'] == EquipmentStatus::WORK)
                    $color = 'background-color: green';
                $list[$stat['uuid']] = $stat['title'];
                $status[$stat['uuid']] = "<span class='badge' style='" . $color . "; height: 12px; margin-top: -3px'> </span>&nbsp;" .
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
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'serial',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->serial;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'testDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            return date("d-m-Y", strtotime($data->testDate));
        },
        'editableOptions' => [
            'header' => 'Дата предыдущей поверки',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' => 'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATE,
                'displayFormat' => 'dd-MM-yyyy',
                'saveFormat' => 'php:Y-m-d H:m:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'inputDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            return date("d-m-Y", strtotime($data->inputDate));
        },
        'editableOptions' => [
            'header' => 'Дата ввода в эксплуатацию',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' => 'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATE,
                'displayFormat' => 'dd-MM-yyyy',
                'saveFormat' => 'php:Y-m-d H:m:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Ответственный исполнитель',
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            $userSystems = UserSystem::find()
                ->where(['equipmentSystemUuid' => $data['equipmentType']['equipmentSystem']['uuid']])
                ->all();
            $count = 0;
            $userEquipmentName = '';
            foreach ($userSystems as $userSystem) {
                if ($count > 0) $userEquipmentName .= ', ';
                $userEquipmentName .= $userSystem['user']['name'];
                $count++;
            }
            if ($count == 0) $userEquipmentName = '<div class="progress"><div class="critical5">не назначен</div></div>';
            return $userEquipmentName;
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'buttons'=>[
            'add' => function ($url,$model) {
                return Html::a('<span class="glyphicon glyphicon-comment"></span>&nbsp',
                    ['../task/form', 'equipmentUuid' => $model['uuid']],
                    [
                        'title' => 'Добавить задачу',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalTask',
                    ]
                );
            }
        ],
        'template' => '{add} {update} ',
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
            Html::a('Добавить',
                ['../equipment/add'],
                [
                    'class' => 'btn btn-success',
                    'title' => 'Добавить оборудование',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAdd',
                ]
            ),
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
    'summary' => '',
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

$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
     window.location.replace("../equipment/index");
})');

$this->registerJs('$("#modalTask").on("hidden.bs.modal",
function () {
window.location.replace("../equipment/index");
})');
?>

<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalTask">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
