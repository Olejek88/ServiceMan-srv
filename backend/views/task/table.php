<?php
/* @var $searchModel backend\models\TaskSearch */

use common\components\MainFunctions;
use common\models\Users;
use common\models\WorkStatus;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Таблица задач');

$gridColumns = [
    [
        'attribute' => '_id',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'value' => function () {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model) {
            return Yii::$app->controller->renderPartial('task-details', ['model' => $model]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['taskTemplate']->title;
        }
    ],
    [
        'attribute' => 'comment',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (isset($data['comment'])) {
                return $data['comment'];
            } else {
                return 'неизвестно';
            }
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'workStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'editableOptions'=> function () {
            $status=[];
            $list=[];
            $statuses = WorkStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color='background-color: white';
                if ($stat['uuid']==WorkStatus::CANCELED ||
                    $stat['uuid']==WorkStatus::NEW_OPERATION)
                    $color='background-color: gray';
                if ($stat['uuid']==WorkStatus::IN_WORK)
                    $color='background-color: yellow';
                if ($stat['uuid']==WorkStatus::UN_COMPLETE)
                    $color='background-color: lightred';
                if ($stat['uuid']==WorkStatus::COMPLETE)
                    $color='background-color: green';
                $list[$stat['uuid']] = $stat['title'];
                $status[$stat['uuid']] = "<span class='badge' style='".$color."; height: 12px; margin-top: -3px'> </span>&nbsp;".
                    $stat['title'];
            }
            return [
                'header' => 'Статус задачи',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
        'value' => function ($model) {
            $status =MainFunctions::getColorLabelByStatus($model['workStatus'],'task_status');
            return $status;
        },

        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(WorkStatus::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw'
    ],
    [
        'attribute' => 'taskVerdictUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model) {
            $status =MainFunctions::getColorLabelByStatus($model['taskVerdict'],'task_verdict');
            return $status;
        },
        'format' => 'raw'
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'filterType' => GridView::FILTER_DATETIME,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'editableOptions' => [
            'header' => 'Дата назначения',
            'size' => 'md',
            'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'displayFormat' => 'yyyy-MM-dd hh:mm:ss',
                'saveFormat' => 'php:Y-m-d h:m:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
/*    [
        'class' => 'kartik\grid\EditableColumn',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Исполнители',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions'=> function () {
            $users = ArrayHelper::map(Users::find()->orderBy('name')->all(), 'uuid', 'name');
            return [
                'header' => 'Исполнитель задачи',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $users,
                'data' => $users
            ];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Users::find()->orderBy('name')->all(),
            'uuid', 'name'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true]
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
    ],*/
    [
        'attribute' => 'startDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'attribute' => 'endDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Закрыта задача',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->endDate)>0)
                return date("Y-m-d h:m", strtotime($data->endDate));
            else
                return 'не закрыта';
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Основание',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            // или автоматически по расписанию
            return 'заявка';
        }
    ],
 ];

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
            '<form action="table"><div class="row" style="margin-bottom: 8px; width:100%"><div class="col-sm-4" style="width:34%">'.
            DateTimePicker::widget([
                'name' => 'start_time',
                'value' => '2018-12-01 00:00:00',
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</div><div class="col-sm-4" style="width:34%">'.
            DateTimePicker::widget([
                'name' => 'end_time',
                'value' => '2021-12-31 00:00:00',
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</div><div class="col-sm-2" style="width:12%">'.Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']).'</div><div class="col-sm-2" style="width:12%">'.
            Html::a('Новая', ['/task/create'], ['class'=>'btn btn-success']).'</div>'.
            '<div class="col-sm-1" style="width:8%">'.'{export}'.'</div></div></form>',
            'options' => ['style' => 'width:100%']
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'tasks'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => true,
    'responsive' => false,
    'hover' => true,
    'floatHeader' => false,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Задачи',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
