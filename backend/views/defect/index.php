<?php
/* @var $searchModel backend\models\DefectSearch */

use common\models\DefectType;
use common\models\User;
use common\models\Users;
use kartik\date\DatePicker;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
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
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'title',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'equipment.title',
        'header' => 'Элемент',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'defectTypeUuid',
        'header' => 'Тип дефекта',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(DefectType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'headerOptions' => ['class' => 'text-center'],
        'format' => 'raw',
        'content' => function ($data) {
            return $data['defectType']['title'];
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'header' => 'Адрес'.'<table><tr><form action=""><td>'.Html::textInput('address','',['style' => 'width:100%']).'</td></form></tr></table>',
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
                'size' => 'sm',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'attribute' => 'userUuid',
        'value' => 'user.name',
        'header' => 'Исполнитель',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '200px',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Users::find()->joinWith('user')
            ->andWhere(['user.status' => User::STATUS_ACTIVE])
            ->orderBy('name')->all(), 'uuid', 'name'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
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

$start_date = '2018-12-31';
$end_date = '2021-12-31';
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];

echo GridView::widget([
    'filterSelector' => '.add-filter',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action=""><table style="width: 800px; padding: 3px"><tr><td style="width: 300px">' .
            DatePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pjaxContainerId' => 'defect-table',
                'class' => ['add-filter'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd 00:00:00'
                ]
            ]) . '</td><td style="width: 300px">' .
            DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pjaxContainerId' => 'defect-table',
                'class' => ['add-filter'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd 00:00:00'
                ]
            ]) . '</td><td style="width: 100px">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']) . '</td><td style="width: 100px">{export}</td></tr></table></form>',
            'options' => ['style' => 'width:100%']
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'event'
    ],
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'defect-table',
        ],
    ],
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
    'rowOptions' => function ($model) {
        if (isset($_GET['uuid'])) {
            if ($_GET['uuid'] == $model['uuid'])
                return ['class' => 'danger'];
        }
    }
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

