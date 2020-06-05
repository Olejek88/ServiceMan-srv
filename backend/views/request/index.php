<?php
/* @var $searchModel backend\models\RequestSearch */

use common\models\Contragent;
use common\models\Request;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Task;
use common\models\Users;
use common\models\WorkStatus;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Журнал диспетчера');

$gridColumns = [
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'serialNumber',
        'vAlign' => 'middle',
        'header' => 'Номер заявки',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center;'
        ],
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
            if (strtotime($data->createdAt) > 0)
                return date("d-m-Y H:i", strtotime($data->createdAt));
            else
                return 'не открыт';
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
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
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'editableOptions' => function () {
            $types = [
                0 => "Бесплатная заявка", 1 => "Платная заявка"
            ];
            $status = ["<span class='badge' style='background-color: seagreen; height: 22px;'>Бесплатная</span>",
                "<span class='badge' style='background-color: darkorange; height: 22px;'>Платная</span>"];
            $list = $types;
            return [
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
    ],
    [
        'attribute' => 'contragent',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Заявитель',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            /** @var Request $data */
            if ($data->contragentUuid != null && $data->contragentUuid != Contragent::DEFAULT_CONTRAGENT) {
                return $data->contragent->title . '<br/> [' . $data->contragent->phone . ']';
            } else {
                if ($data->authorUuid == Users::USER_SERVICE_UUID) {
                    return Users::USER_SERVICE_TITLE;
                } else {
                    return $data->author->name;
                }
            }
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
            /** @var Request $data */
            if ($data->authorUuid == Users::USER_SERVICE_UUID) {
                return Users::USER_SERVICE_TITLE;
            } else {
                return $data->author->name;
            }
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
                return "<span class='badge' style='background-color: green; height: 22px;'>" . $model['requestStatus']['title'] . "</span>";
            else if ($model['requestStatusUuid'] == RequestStatus::NEW_REQUEST)
                return "<span class='badge' style='background-color: lightcoral; height: 22px;'>" . $model['requestStatus']['title'] . "</span>";
            else
                return "<span class='badge' style='background-color: gray; height: 22px;'>" . $model['requestStatus']['title'] . "</span>";
        },
        'editableOptions' => function () {
            $status = [];
            $list = [];
            $statuses = RequestStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                if ($stat['uuid'] == RequestStatus::COMPLETE)
                    $status[$stat['uuid']] = "<span class='badge' style='background-color: green; height: 22px;'>" . $stat['title'] . "</span>";
                else if ($stat['uuid'] == RequestStatus::NEW_REQUEST)
                    $status[$stat['uuid']] = "<span class='badge' style='background-color: lightcoral; height: 22px;'>" . $stat['title'] . "</span>";
                else
                    $status[$stat['uuid']] = "<span class='badge' style='background-color: gray; height: 22px;'>" . $stat['title'] . "</span>";
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
        'attribute' => 'objectUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Адрес ' . Html::a('<span class="fa fa-search"></span>&nbsp',
                ['../request/search-form'],
                [
                    'title' => 'Фильтрация по адресу',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalFilter',
                ]
            ) . '&nbsp' . Html::a('<span class="fa fa-close"></span>&nbsp',
                ['../request']),
        'mergeHeader' => true,
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        /*        'editableOptions' => function () {
                    $list = [];
                    $objects = Objects::find()->orderBy('houseUuid,title')->all();
                    foreach ($objects as $object) {
                        $list[] = $object->getFullTitle();
                    }
                    return [
                        'size' => 'md',
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'displayValueConfig' => $list,
                        'data' => $list
                    ];
                },*/

        'value' => function ($model) {
            if ($model->objectUuid)
                return "<span style='display: inline-block !important; height: 22px;'>" . $model['object']->getFullTitle() . "</span>";
            else
                return "<span style='height: 22px; display: inline-block !important;'>нет</span>";
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
            /** @var Request $model */
            return $model->requestType->title;
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(RequestType::find()->orderBy('title')->all(),
            'uuid', function ($data) {
                return $data['title'];
            }),
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
                        ['../task/info', 'task' => $model['taskUuid']],
                        [
                            'title' => 'Просмотреть задачу',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalTaskInfo',
                        ]);
                    $order .= ' ';
                    if ($task['workStatusUuid'] == WorkStatus::COMPLETE) {
                        $order .= "<span class='badge' style='background-color: green; height: 22px;'>Выполнена</span>";
                    }
                    if ($task['workStatusUuid'] == WorkStatus::NEW)
                        $order .= "<span class='badge' style='background-color: gray; height: 22px;'>Новая</span>";
                    if ($task['workStatusUuid'] == WorkStatus::CANCELED)
                        $order .= "<span class='badge' style='background-color: lightskyblue; height: 22px;'>Отменена</span>";
                    if ($task['workStatusUuid'] == WorkStatus::UN_COMPLETE)
                        $order .= "<span class='badge' style='background-color: orangered; height: 22px;'>Не завершена</span>";
                    $order .= '<br/>' . $task['taskTemplate']['title'];
                    return $order;
                }
            }
            return Html::a("<span class='badge' style='background-color: lightgrey; height: 22px;'>не создавалась</span>",
                ['../task/form', 'equipmentUuid' => $model['equipmentUuid'], 'requestUuid' => $model['uuid'],
                    'type_uuid' => $model['equipment']['equipmentTypeUuid']],
                [
                    'title' => 'Добавить задачу',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalTask',
                ]
            );
        },
    ],
//    [
//        'attribute' => 'contragent',
//        'hAlign' => 'center',
//        'vAlign' => 'middle',
//        'header' => 'Исполнитель',
//        'format' => 'raw',
//        'headerOptions' => ['class' => 'kartik-sheet-style'],
//        'mergeHeader' => true,
//        'value' => function ($model) {
//            return "<span class='badge' style='background-color: gray; height: 22px;'>" . $model['contragent']['title'] . "</span>";
//        },
//        'contentOptions' => [
//            'class' => 'table_class'
//        ],
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => ArrayHelper::map(Contragent::find()->orderBy('title')->all(),
//            'uuid', 'title'),
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => 'Любой'],
//    ],
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
        'editableOptions' => ['placement' => PopoverX::ALIGN_LEFT]
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'buttons' => [
            'edit' => function ($url, $model) {
                return Html::a('<span class="fa fa-edit"></span>',
                    ['../request/form', 'uuid' => $model['uuid']],
                    [
                        'title' => 'Редактировать заявку',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalRequest',
                    ]
                );
            },
            'history' => function ($url, $model) {
                return Html::a('<span class="fa fa-history"></span>',
                    ['../request/history', 'uuid' => $model['uuid']],
                    [
                        'title' => 'История изменения',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalRequestHistory',
                    ]
                );
            },
            'messages' => function ($url, $model) {
                return Html::a('<span class="fa fa-comments"></span>',
                    ['../request/messages', 'uuid' => $model['uuid']],
                    [
                        'title' => 'Сообщения',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalMessages',
                    ]
                );
            },
            'add' => function ($url, $model) {
                if (empty($model['extId'])) {
                    return '';
                }

                return Html::a('<span class="fa fa-comment"></span>',
                    ['../request/add-message', 'uuid' => $model['uuid'],
                        'requestId' => $model['extId']],
                    [
                        'title' => 'Добавить сообщение ' . $model['extId'],
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAddComment',
                    ]
                );
            }
        ],
        'template' => ' {edit} {history} {messages} {delete} {add}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

$start_date = date('d-m-Y', time() - 3600 * 24 * 31 * 2 * 12);
$end_date = date('d-m-Y');
$type = '';
if (isset($_GET['type']))
    $type = $_GET['type'];
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];

echo GridView::widget([
    'id' => 'requests-table',
    'filterSelector' => '.add-filter',
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
            '<form action="/request"><table style="width: 800px; padding: 3px;"><tr><td style="width: 300px;">' .
            DatePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pjaxContainerId' => 'request-table',
                'class' => ['add-filter'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]) . '</td><td style="width: 300px;">' .
            DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pjaxContainerId' => 'request-table',
                'class' => ['add-filter'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]) . '</td><td style="width: 100px;">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-info']) . '</td><td style="width: 150px;">' .
            Html::a('Новая',
                ['../request/form'],
                [
                    'class' => 'btn btn-success',
                    'title' => 'Добавить заявку',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalRequest',
                ]
            )
            . '</td>
            <td style="width: 100px;">{export}</td></tr></table></form>',
            'options' => [
                'style' => [
                    'width' => '100%',
                ],
            ]
        ],
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'requests'
    ],
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'request-table',
        ],
    ],
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => ['line-height' => 0, 'padding' => 0]],
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
        'headingOptions' => ['style' => 'background: #337ab7;']
    ],
    'rowOptions' => function ($model) {
        if (isset($_GET['uuid'])) {
            if ($_GET['uuid'] == $model['uuid'])
                return ['class' => 'danger'];
        }
    }
]);

$this->registerJs('$("#modalRequest").on("hidden.bs.modal",
function () {
     $(this).removeData().find(".modal-content").html("");
//     window.location.reload();
})');
$this->registerJs('$("#modalRequestHistory").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');
$this->registerJs('$("#modalTask").on("hidden.bs.modal",
function () {
     $(this).removeData();
     window.location.reload();
})');
$this->registerJs('$("#modalTaskInfo").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');
$this->registerJs('$("#modalAddComment").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');
$this->registerJs('$("#modalMessages").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');
?>

<style>
    .grid-view td {
        white-space: pre-line;
    }
</style>

<div class="modal remote fade" id="modalRequest">
    <div class="modal-dialog" style="width: 1000px; height: 700px;">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalMessages">
    <div class="modal-dialog" style="width: 1000px; height: 700px;">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalTask">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalTaskInfo">
    <div class="modal-dialog" id="modalTaskContent">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalRequestHistory">
    <div class="modal-dialog" style="width: 800px; height: 400px;">
        <div class="modal-content loader-lg" id="modalContentHistory">
        </div>
    </div>
</div>

<div class="modal remote fade" id="modalAddComment">
    <div class="modal-dialog" style="width: 800px; height: 400px;">
        <div class="modal-content loader-lg" id="modalAddComment">
        </div>
    </div>
</div>

<div class="modal remote fade" id="modalFilter">
    <div class="modal-dialog" style="width: 400px; height: 500px;">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
