<?php
/* @var $searchModel backend\models\RequestSearch */

use common\models\EquipmentStatus;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Task;
use common\models\WorkStatus;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Журнал диспетчера');

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
            return Html::a($data->_id,
                ['../request/form', 'uuid' => $data['uuid']],
                [
                    'title' => 'Редактировать заявку',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalRequest',
                ]
            );
        }
    ],
    [
        'attribute' => 'createdAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Создана',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'content' => function ($data) {
            if (strtotime($data->createdAt)>0)
                return date("d-m-Y H:m", strtotime($data->createdAt));
            else
                return 'не открыт';
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'type',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Тип заявки',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $types = [
                0 => "Бесплатная заявка", 1 => "Платная заявка"
            ];
            return $types[$data["type"]];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [
            0 => "Бесплатная заявка", 1 => "Платная заявка"
        ],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой']
    ],
    [
        'attribute' => 'user',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Заявитель',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['user']->title . '<br/> [' . $data['user']->phone . ']';
        }
    ],
    [
        'attribute' => 'author',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Диспетчер',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['author']->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'requestStatusUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '150px',
        'header' => 'Статус заявки',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => function ($model) {
            if ($model['requestStatusUuid'] == RequestStatus::COMPLETE)
                return "<span class='badge' style='background-color: green; height: 22px'>" . $model['requestStatus']['title'] . "</span>";
            else if ($model['requestStatusUuid'] == RequestStatus::NEW_REQUEST)
                return "<span class='badge' style='background-color: lightcoral; height: 22px'>" . $model['requestStatus']['title'] . "</span>";
            else
                return "<span class='badge' style='background-color: gray; height: 22px'>" . $model['requestStatus']['title'] . "</span>";
        },
        'editableOptions' => function () {
            $status = [];
            $list = [];
            $statuses = RequestStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                if ($stat['uuid'] == RequestStatus::COMPLETE)
                    $status[$stat['uuid']] = "<span class='badge' style='background-color: green; height: 22px'>" . $stat['title'] . "</span>";
                else if ($stat['uuid'] == RequestStatus::NEW_REQUEST)
                    $status[$stat['uuid']] = "<span class='badge' style='background-color: lightcoral; height: 22px'>" . $stat['title'] . "</span>";
                else
                    $status[$stat['uuid']] = "<span class='badge' style='background-color: gray; height: 22px'>" . $stat['title'] . "</span>";
                $list[$stat['uuid']] = $stat['title'];
            }
            return [
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
                /*
                                'displayValueConfig' => $statuses,
                                'data' => $statuses*/
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
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Адрес',
        'mergeHeader' => true,
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'value' => function ($model) {
            if ($model->equipmentUuid) {
                if ($model['equipment']['equipmentStatusUuid'] == EquipmentStatus::WORK)
                    return "<span class='badge' style='background-color: green; height: 22px'>" . $model['equipment']['object']->getFullTitle() . "</span>";
                else
                    return "<span class='badge' style='background-color: lightgrey; height: 22px'>" . $model['equipment']['object']->getFullTitle() . "</span>";
            } else {
                if ($model->objectUuid)
                    return "<span class='badge' style='background-color: lightgrey; height: 22px'>" . $model['object']->getFullTitle() . "</span>";
                else
                    return "<span class='badge' style='background-color: grey; height: 22px; width: 100px'>нет</span>";
            }
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'comment',
        'vAlign' => 'middle',
        'header' => 'Причина обращения',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'requestTypeUuid',
        'vAlign' => 'middle',
        'width' => '150px',
        'header' => 'Характер обращения',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => function ($model) {
            return "<span class='badge' style='background-color: gray; height: 22px'>" . $model['requestType']['title'] . "</span>";
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(RequestType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
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
                    $order = Html::a('Задача №' . $task['_id'],
                        ['../task', 'uuid' => $task['uuid']],
                        [
                            'title' => 'Редактировать заявку',
                        ]);
                    $order.=' ';
                    if ($task['workStatusUuid'] == WorkStatus::COMPLETE)
                        $order .= "<span class='badge' style='background-color: green; height: 22px'>Выполнена</span>";
                    else
                        $order .= "<span class='badge' style='background-color: sandybrown; height: 22px'>" . $task['workStatus']->title . "</span>";
                    $order .= '<br/>' . $task['taskTemplate']['title'];
                    return $order;
                }
            }
            return Html::a("<span class='badge' style='background-color: lightgrey; height: 22px'>не создавалась</span>",
                ['../task/form', 'equipmentUuid' => $model['equipmentUuid'], 'requestUuid' => $model['uuid']],
                [
                    'title' => 'Добавить задачу',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalTask',
                ]
            );
        },
    ],
/*    [
        'attribute' => 'contragent',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Исполнитель',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'value' => function ($model) {
            return "<span class='badge' style='background-color: gray; height: 22px'>" . $model['contragent']['title'] . "</span>";
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Contragent::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
    ],*/
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'verdict',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Принятое решение',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'result',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Результат контроля',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'buttons'=>[
            'edit' => function ($url,$model) {
                return Html::a('<span class="fa fa-edit"></span>',
                    ['../request/form', 'uuid' => $model['uuid']],
                    [
                        'title' => 'Редактировать заявку',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalRequest',
                    ]
                );
            }
        ],
        'template' => '{edit} {delete}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

$start_date = '2018-12-31';
$end_date = '2021-12-31';
$type = '';
if (isset($_GET['type']))
    $type = $_GET['type'];
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];

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
            '<form action="/request"><table style="width: 800px; padding: 3px"><tr><td style="width: 300px">' .
            DatePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]).'</td><td style="width: 300px">'.
            DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]).'</td><td style="width: 100px">'.Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-info']).'</td><td style="width: 150px">'.
            Html::a('Новая',
                ['../request/form'],
                [
                    'class' => 'btn btn-success',
                    'title' => 'Добавить заявку',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalRequest',
                ]
            )
            .'</td>
            <td style="width: 100px">{export}</td></tr></table></form>',
            'options' => ['style' => 'width:100%']
        ],
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
        'heading' => '<i class="fa fa-tasks"></i>&nbsp; Журнал диспетчера',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
    'rowOptions' => function($model) {
        if (isset($_GET['uuid'])){
            if ($_GET['uuid'] == $model['uuid'])
                return ['class' => 'danger'];
        }
    }
]);

$this->registerJs('$("#modalRequest").on("hidden.bs.modal",
function () {
     window.location.replace("../request/index");
})');
$this->registerJs('$("#modalTask").on("hidden.bs.modal",
function () {
     window.location.replace("../request/index");
})');

?>
<div class="modal remote fade" id="modalRequest">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalTask">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
