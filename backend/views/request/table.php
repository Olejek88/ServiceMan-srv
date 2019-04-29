<?php
/* @var $searchModel backend\models\RequestSearch */

use common\models\EquipmentStatus;
use common\models\RequestStatus;
use common\models\Stage;
use common\models\StageStatus;
use common\models\Task;
use common\models\WorkStatus;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Управление заявками');

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
        'mergeHeader' => true,
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'attribute' => 'user',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Автор',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['user']->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'requestStatusUuid',
        'vAlign' => 'middle',
        'width' => '150px',
        'header' => 'Статус заявки',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => 'requestStatus.title',
        'editableOptions' => function () {
            $statuses = ArrayHelper::map(RequestStatus::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $statuses,
                'data' => $statuses
            ];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(RequestStatus::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
    ],
    [
        'attribute' => 'equipment',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Оборудование',
        'mergeHeader' => true,
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'value' => function ($model) {
            if ($model->equipmentUuid) {
                if ($model['equipment']['equipmentStatusUuid']==EquipmentStatus::WORK)
                    return "<span class='badge' style='background-color: green; height: 22px'>" . $model['equipment']->title . "</span>";
                else
                    return "<span class='badge' style='background-color: lightgrey; height: 22px'>" . $model['equipment']->title . "</span>";
            }
            else return "<span class='badge' style='background-color: grey; height: 22px; width: 100px'>нет</span>";
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'object',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Объект',
        'mergeHeader' => true,
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'value' => function ($model) {
            if ($model->objectUuid)
                return "<span class='badge' style='background-color: lightgrey; height: 22px'>" . $model['object']->title . "</span>";
            else
                return "<span class='badge' style='background-color: grey; height: 22px; width: 100px'>нет</span>";
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'comment',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Комментарий',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Изменение',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Задача',
        'mergeHeader' => true,
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'value' => function ($model) {
            if ($model['taskUuid']) {
                $task = Task::find()->where(['uuid' => $model['taskUuid']])->one();
                if ($task) {
                    $order = 'Задача №'.$task['_id'].' ['.$task['taskTemplate']['title'].']';
                    if ($task['workStatusUuid']==WorkStatus::COMPLETE)
                        return "<span class='badge' style='background-color: green; height: 22px'>".$order." [Выполнен]</span>";
                    else
                        return "<span class='badge' style='background-color: sandybrown; height: 22px'>".$order." [".$task['workStatus']->title."]</span>";
                }
            }
            return "<span class='badge' style='background-color: lightgrey; height: 22px'>не создавалась</span>";
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'closeDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Закрыт',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'value' => function ($model) {
            if ($model->closeDate)
                return "<span class='badge' style='background-color: green; height: 22px'>".$model->closeDate."</span>";
            else return "-";
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'template'=> '{delete}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'requests-table',
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
            Html::a('Новая', ['/request/create'], ['class' => 'btn btn-success'])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'requests'
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
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Заявки',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
