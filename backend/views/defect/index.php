<?php
/* @var $searchModel backend\models\DefectSearch */

use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Дефекты');

$gridColumns = [
    [
        'attribute' => 'date',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '120px',
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'title',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'equipment.title',
        'header' => 'Элементы',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'defectType.title',
        'header' => 'Тип дефекта',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'header' => 'Адрес',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return $model['equipment']['object']->getFullTitle();
        },

    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'defectStatus',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '120px',
        'value' => function ($model) {
            if ($model['defectStatus'])
                return '<div class="progress"><div class="critical5">Обработан</div></div>';
            else
                return '<div class="progress"><div class="critical1">Не обработан</div></div>';
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ['0' => 'Не обработан', '1' => 'Обработан'],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => function ($model, $key, $index, $widget) {
            $models = ['0' => 'Не обработан', '1' => 'Обработан'];
            return [
                'header' => 'Статус',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'attribute' => 'user.name',
        'header' => 'Исполнитель',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '200px',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'width' => '180px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'attribute' => 'task.taskTemplate.title',
        'header' => 'Название задачи',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($model) {
            if ($model['task'])
                $task =  "<span class='badge' style='background-color: green; height: 22px; margin-top: -3px'>".$model['task']['taskTemplate']['title']."</span>";
            else
                $task =  "<span class='badge' style='background-color: gray; height: 22px; margin-top: -3px'>не назначена</span>";

            $task = Html::a($task,
                ['/equipment/select-task', 'equipmentUuid' => $model['equipmentUuid'],
                    'defectUuid' => $model['uuid']],
                [
                    'title' => 'Создать задачу обслуживания',
                    'data-toggle' => 'modal',
                    'data-pjax' => '0',
                    'data-target' => '#modalAddTask'
                ]
            );
            return $task;

        }
    ]
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('Новый', ['/defect/create'], ['class' => 'btn btn-success']),
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
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
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; Дефекты',
        'headingOptions' => ['style' => 'background: #337ab7']

    ],
]);

$this->registerJs('$("#modalAddTask").on("hidden.bs.modal",
function () {
     window.location.replace("../defect/index");
})');

?>

<div class="modal remote fade" id="modalAddTask">
    <div class="modal-dialog" style="width: 400px; height: 300px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>

