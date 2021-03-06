<?php

use common\components\MainFunctions;
use common\models\TaskVerdict;
use common\models\User;
use common\models\Users;
use common\models\WorkStatus;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\select2\Select2;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Таблица задач');
$users = Users::find()
    ->joinWith('user')
    ->andWhere(['user.status' => User::STATUS_ACTIVE])
    ->all();
$items = ArrayHelper::map($users, 'uuid', 'name');

$gridColumns = [
    [
        'attribute' => '_id',
        'vAlign' => 'middle',
        'mergeHeader' => true,
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
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'header' => 'Задача',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['taskTemplate']->title;
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Адрес',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['equipment']['title'];
        }
    ],
    [
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'header' => 'Адрес',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['equipment']['object']->getFullTitle();
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'workStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(WorkStatus::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'editableOptions'=> function () {
            $status=[];
            $list=[];
            $statuses = WorkStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color='background-color: white';
                if ($stat['uuid']==WorkStatus::CANCELED ||
                    $stat['uuid']==WorkStatus::NEW)
                    $color='background-color: gray';
                if ($stat['uuid']==WorkStatus::IN_WORK)
                    $color = 'background-color: gray';
                if ($stat['uuid']==WorkStatus::UN_COMPLETE)
                    $color = 'background-color: orange';
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
            $status =MainFunctions::getColorLabelByStatus($model['workStatus'],'work_status_edit');
            return $status;
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'taskVerdictUuid',
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(TaskVerdict::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
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
        'attribute' => 'taskDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Назначена',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->taskDate))
                return date("d-m-Y H:i", strtotime($data->taskDate));
            else
                return 'не назначена';
        },
        'editableOptions' => [
            'header' => 'Дата назначения',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'yyyy-MM-dd hh:mm:ss',
                'saveFormat' => 'php:Y-m-d H:i:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
    [
        'attribute' => 'startDate',
        'header' => 'Начало',
        'hAlign' => 'center',
        'mergeHeader' => true,
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->startDate))
                return date("d-m-Y H:i", strtotime($data->startDate));
            else
                return 'не начата';
        }
    ],
    [
        'attribute' => 'endDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Закончена',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->endDate))
                return date("d-m-Y H:i", strtotime($data->endDate));
            else
                return 'не закрыта';
        }
    ],
    [
        'attribute'=>'authorUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' =>[
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if ($data['author'])
                return $data['author']->name;
            else
                return 'отсутствует';
        }
    ],
    [
        'attribute' => 'comment',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
        'content' => function ($data) {
            if (isset($data['comment'])) {
                return $data['comment'];
            } else {
                return 'неизвестно';
            }
        }
    ]
];

$start_date = '2018-12-31';
$end_date = '2021-12-31';
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];
$user='';
if (isset($_GET['user']))
    $user = $_GET['user'];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action="/task/table-user"><table style="width: 100%"><tr>
            <td style="margin: 3px; padding: 3px">' .
            Select2::widget([
                'name' => 'user',
                'language' => 'ru',
                'value' => $user,
                'data' => $items,
                'options' => ['placeholder' => 'Все исполнители'],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]) . '</td><td style="margin: 3px; padding: 3px">'.
            DateTimePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</td><td style="margin: 3px; padding: 3px">'.
            DateTimePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</td><td style="margin: 3px; padding: 3px">'.Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']).'</td><td>{export}</td></tr></table></form>',
            'options' => ['style' => 'width:100%']
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'tasks'
    ],
    'pjax' => true,
    'options' => ['style' => 'width:100%'],
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
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Выполненные задачи',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

/** @var $dataProvider2 */

echo GridView::widget([
    'dataProvider' => $dataProvider2,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'containerOptions' => ['style' => 'overflow: auto'],
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
    'toolbar' => [
        ['content' =>
            '<table style="width: 100%"><tr><td style="align-content: end">{export}</td></tr></table>',
            'options' => ['style' => 'width:100%']
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'tasks'
    ],
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Задачи в работе',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
