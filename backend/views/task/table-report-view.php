<?php
/* @var $searchModel backend\models\TaskSearch
 * @var $titles
 * @var $warnings
 */

use common\components\MainFunctions;
use common\models\Defect;
use common\models\Measure;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Photo;
use common\models\Request;
use common\models\Settings;
use common\models\TaskVerdict;
use common\models\User;
use common\models\Users;
use common\models\WorkStatus;
use kartik\date\DatePicker;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

if (!isset($titles))
    $titles = "Журнал задач";
$this->title = Yii::t('app', 'ТОИРУС ЖКХ::' . $titles);

$type = '';
if (isset($_GET['type']))
    $type = $_GET['type'];
$request = '';
if (isset($_GET['request']))
    $type = $_GET['request'];

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'taskDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'enableSorting' => TRUE,
        //'mergeHeader' => true,
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->taskDate) > 0)
                return date("d-m-Y H:i", strtotime($data->taskDate));
            else
                return 'не назначен';
        },
        'editableOptions' => function ($data) {
            if ($data['workStatusUuid'] == WorkStatus::NEW)
                return [
                    'header' => 'Дата назначения',
                    'size' => 'md',
                    'inputType' => Editable::INPUT_WIDGET,
                    'widgetClass' => 'kartik\datecontrol\DateControl',
                    'options' => [
                        'type' => DateControl::FORMAT_DATETIME,
                        'displayFormat' => 'dd-MM-yyyy HH:mm',
                        'saveFormat' => 'php:Y-m-d H:i:s',
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'autoclose' => true
                            ]
                        ]
                    ]
                ];
            else
                return [
                    'header' => 'Дату назначения нельзя после начала работ',
                    'readonly' => true
                ];
        }
    ],
    [
        'attribute' => 'authorUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Users::find()
            ->joinWith('user')
            ->andWhere(['user.status' => User::STATUS_ACTIVE])
            ->where(['!=', 'uuid', Users::USER_SERVICE_UUID])
            ->orderBy('name')
            ->all(),
            'uuid', 'name'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if ($data['authorUuid'])
                return $data['author']->name;
            else
                return 'отсутствует';
        }
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => 'Заявка',
        /*    . '<table><tr><form action=""><td>' .
                    Select2::widget([
                        'id' => 'request',
                        'name' => 'request',
                        'language' => 'ru',
                        'data' => [
                            '0' => 'Бесплатная заявка',
                            '1' => 'Платная заявка'
                        ],
                        'value' => $request,
                        'options' => ['placeholder' => 'Тип заявки'],
                        'pluginEvents' => [
                            "select2:select" => "function() {
                                window.location.replace('table?request='+document.getElementById('request').value);
                            }"
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])
                    . '</td></form></tr></table>',*/
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $request = Request::find()->where(['taskUuid' => $data['uuid']])->one();
            if ($request) {
                $name = "<span class='badge' style='background-color: lightblue; height: 22px'>Заявка #" . $request['_id'] . "</span>";
                $link = Html::a($name, ['../request/index', 'uuid' => $request['uuid']], ['title' => 'Заявка']);
                $type = "<span class='badge' style='background-color: seagreen; height: 22px'>Бесплатная</span>";
                if ($request['type'] == 1)
                    $type = "<span class='badge' style='background-color: darkorange; height: 22px'>Платная</span>";
                return $link . '<br/>' . $type;
            } else
                return "без заявки";
        },
    ],
    [
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'header' => 'Задача' . '<table><tr><form action=""><td>' .
            Select2::widget([
                'id' => 'type',
                'name' => 'type',
                'language' => 'ru',
                'data' => [
                    '0' => 'Выполненные в срок',
                    '1' => 'Не выполненные в срок',
                    '2' => 'Выполненные не в срок',
                    '3' => 'Отмененные'
                ],
                'value' => $type,
                'options' => ['placeholder' => 'Статус по времени'],
                'pluginEvents' => [
                    "select2:select" => "function() {
                        window.location.replace('table?type='+document.getElementById('type').value); 
                        }",
                    "select2:unselecting" => "function() {
                        window.location.replace('table');
                    }"
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ])
            . '</td></form></tr></table>',
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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'comment',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Комментарий',
        'editableOptions' => function ($data) {
            if ($data['workStatusUuid'] == WorkStatus::NEW)
                return [];
            else
                return [
                    'header' => ' комментарий нельзя после начала работ',
                    'readonly' => true
                ];
        }
    ],
    [
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => 'Адрес ' . Html::a('<span class="fa fa-search"></span>&nbsp',
                ['../request/search-form'],
                [
                    'title' => 'Фильтрация по адресу',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalFilter',
                ]
            ) . '&nbsp' . Html::a('<span class="fa fa-close"></span>&nbsp',
                ['../task']),
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $house = $data['equipment']['object']['house'];
            if ($data['equipment']['object'] != ObjectType::OBJECT_TYPE_FLAT)
                return 'ул.' . $house['street']['title'] . ', д.' . $house['number'] . ' - ' .
                    $data['equipment']['equipmentType']['equipmentSystem']['title'];
            else
                return 'ул.' . $house['street']['title'] . ', д.' . $house['number'] . ' - ' .
                    $data['equipment']['equipmentType']['equipmentSystem']['title'];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Objects::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой']
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => 'Элемент',
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['equipment']['title'];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'deadlineDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->deadlineDate) > 0)
                return date("d-m-Y H:i", strtotime($data->deadlineDate));
            else
                return 'не задан';
        },
        'editableOptions' => function ($data) {
            if ($data['workStatusUuid'] == WorkStatus::NEW)
                return [
                    'header' => 'Срок',
                    'size' => 'md',
                    'inputType' => Editable::INPUT_WIDGET,
                    'widgetClass' => 'kartik\datecontrol\DateControl',
                    'options' => [
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'autoclose' => true
                            ]
                        ],
                        'type' => DateControl::FORMAT_DATETIME,
                        'displayFormat' => 'dd-MM-yyyy HH:mm',
                        'saveFormat' => 'php:Y-m-d H:i:s'
                    ]
                ];
            else
                return [
                    'header' => 'Дату назначения нельзя после начала работ',
                    'readonly' => true
                ];
        }
    ],
    [
        'header' => 'Исполнители',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Users::find()
            ->joinWith('user')
            ->andWhere(['user.status' => User::STATUS_ACTIVE])
            ->orderBy('name')
            ->all(),
            'uuid', 'name'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $users = $data['users'];
            $users_list = "";
            $cnt = 0;
            foreach ($users as $user) {
                if ($cnt > 0) $users_list .= ', ';
                $users_list .= $user['name'];
                $cnt++;
            }
            if ($cnt > 0) {
                if ($data['workStatusUuid'] == WorkStatus::NEW) {
                    $link = Html::a($users_list,
                        ['../task/user', 'taskUuid' => $data['uuid']],
                        [
                            'title' => 'Исполнители',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalUser'
                        ]);
                } else {
                    $link = $users_list;
                }
                if ($data['workStatusUuid'] != WorkStatus::NEW) $link = $users_list;
                return $link;
            } else {
                $name = "<span class='badge' style='background-color: gray; height: 22px'>Не назначены</span>";
                $link = Html::a($name,
                    ['../task/user', 'taskUuid' => $data['uuid']],
                    [
                        'title' => 'Исполнители',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalUser'
                    ]);
                if ($data['workStatusUuid'] != WorkStatus::NEW) $link = $name;
                return $link;
            }
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'workStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(WorkStatus::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'editableOptions' => function () {
            $status = [];
            $list = [];
            $statuses = WorkStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color = 'background-color: gray';
                if ($stat['uuid'] == WorkStatus::CANCELED ||
                    $stat['uuid'] == WorkStatus::NEW)
                    $color = 'background-color: gray';
                if ($stat['uuid'] == WorkStatus::IN_WORK)
                    $color = 'background-color: gray';
                if ($stat['uuid'] == WorkStatus::UN_COMPLETE)
                    $color = 'background-color: orange';
                if ($stat['uuid'] == WorkStatus::COMPLETE)
                    $color = 'background-color: green';
                $list[$stat['uuid']] = $stat['title'];
                $status[$stat['uuid']] = "<span class='badge' style='" . $color . "; height: 12px; margin-top: -3px'> </span>&nbsp;" .
                    $stat['title'];
            }
            return [
                'placement' => PopoverX::ALIGN_LEFT,
                'header' => 'Статус задачи',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
        'value' => function ($model) {
            $status = MainFunctions::getColorLabelByStatus($model['workStatus'], 'work_status_edit');
            if ($model['workStatusUuid'] == WorkStatus::CANCELED) {
                $status .= Html::a('<span class="fa fa-refresh"></span>',
                    ['../task/refresh', 'uuid' => $model['uuid']], ['title' => 'Повторно создать задачу']);
            }
            return $status;
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'taskVerdictUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(TaskVerdict::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'value' => function ($model) {
            $status = MainFunctions::getColorLabelByStatus($model['taskVerdict'], 'task_verdict');
            $images = Photo::find()->where(['objectUuid' => $model['uuid']])->all();
            $cnt = 0;
            foreach ($images as $image) {
                if ($cnt == 0)
                    $status .= '<br/>Изображения: ';
                $path = $image->getImageUrl();
                //'storage/' . Users::getCurrentOid() . '/photo/' . $image['objectUuid'] . '/' . $image['uuid'];
                $status .= Html::a('<span class="fa fa-photo"></span>', $path);
                $cnt++;
            }
            $measure = Measure::find()
                ->where(['equipmentUuid' => $model['equipmentUuid']])
                ->orderBy('date desc')
                ->one();
            if ($measure) {
                $status .= '<br/>Измерения: ' . $measure['value'];
            }
            $defects = Defect::find()->where(['taskUuid' => $model['uuid']])->all();
            $cnt = 0;
            foreach ($defects as $defect) {
                if ($cnt == 0)
                    $status .= '<br/>Дефекты: ';
                $status .= Html::a('<span class="fa fa-warning"></span>&nbsp;' . $defect['title'],
                    ['../defect/index', 'uuid' => $defect['uuid']]);
                $cnt++;
            }

            return $status;
        },
        'format' => 'raw'
    ],
    /*    [
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'header' => 'Операции',
            'mergeHeader' => true,
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'content' => function ($data) {
                $operation_list = "";
                $count = 1;
                $operations = Operation::find()->where(['taskUuid' => $data['uuid']])->all();
                foreach ($operations as $operation) {
                    $operation_list = $count.'. '.$operation['operationTemplate']['title'].'</br>';
                    $count++;
                }
                return $operation_list;
            }
        ],*/
    /*    [
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'header' => 'Дата начала',
            'mergeHeader' => true,
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'content' => function ($data) {
                if (strtotime($data->startDate) > 0)
                    return date("d-m-Y H:i", strtotime($data->startDate));
                else
                    return 'не начата';
            }
        ],*/
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Дата выполнения',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->endDate) > 0)
                return date("d-m-Y H:i", strtotime($data->endDate));
            else
                return 'не закончена';
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Действия',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'buttons' => [
            'measure' => function ($url, $model) {
                return Html::a('<span class="fa fa-bar-chart"></span>',
                    ['../task/measures', 'uuid' => $model['equipmentUuid'], 'date' => $model['startDate']],
                    [
                        'title' => 'Измерения',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalMeasure',
                    ]
                );
            },
            'photo' => function ($url, $model) {
                return Html::a('<span class="fa fa-photo"></span>',
                    ['../task/photos', 'uuid' => $model['uuid']],
                    [
                        'title' => 'Фотографии',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalPhoto',
                    ]
                );
            },
            'defect' => function ($url, $model) {
                return Html::a('<span class="fa fa-warning"></span>',
                    ['../task/defects', 'uuid' => $model['equipmentUuid'], 'date' => $model['startDate']],
                    [
                        'title' => 'Дефекты',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalDefects',
                    ]
                );
            },
            'refresh' => function ($url, $model) {
                if ($model['workStatusUuid'] == WorkStatus::CANCELED) {
                    return Html::a('<span class="fa fa-refresh"></span>',
                        ['../task/refresh', 'uuid' => $model['uuid']], ['title' => 'Повторно создать задачу']);
                }
                return '';
            }
        ],
        'template' => '{measure} {photo} {defect} {refresh}',
    ]
];

$show = Settings::getSettings(Settings::SETTING_SHOW_WARNINGS);
if ($show && $show == '1') {
    foreach ($warnings as $warning) {
        if ($warning != '') {
            echo '<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-ban"></i> Внимание!</h4>';
            echo $warning;
            echo '</div>';
        }
    }
}

$start_date = date('d-m-Y', time() - 3600 * 24 * 31 * 2 * 12);
$end_date = date('d-m-Y');
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];

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
            '<form action=""><table style="width: 800px; padding: 3px"><tr><td style="width: 300px">' .
            DatePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]) . '</td><td style="width: 300px">' .
            DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]) . '</td><td style="width: 100px">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']) . '</td><td style="width: 100px">{export}</td></tr></table></form>',
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
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => true,
    'responsive' => false,
    'hover' => true,
    'floatHeader' => false,
    /*    'panelTemplate' =>
            '<div class="panel {type}">
            {panelHeading}
            {panelBefore}
            <img src="/images/1.png">
            {items}
            {panelAfter}
            {panelFooter}
        </div>',*/
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; ' . $titles,
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
    'rowOptions' => function ($model) {
        if ($model['workStatusUuid'] != WorkStatus::COMPLETE && (strtotime($model['deadlineDate']) <= time()))
            return ['class' => 'danger'];
        if ($model['workStatusUuid'] == WorkStatus::COMPLETE && (strtotime($model['deadlineDate']) < strtotime($model['endDate'])))
            return ['class' => 'warning'];
        if ($model['workStatusUuid'] == WorkStatus::CANCELED)
            return ['class' => 'info'];
        if (isset($_GET['uuid'])) {
            if ($_GET['uuid'] == $model['uuid'])
                return ['class' => 'danger'];
        }
    }
]);

$this->registerJs('$("#modalUser").on("hidden.bs.modal",
function () {
     window.location.reload();
})');

$this->registerJs('$("#modalMeasure").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');

$this->registerJs('$("#modalDefects").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');

$this->registerJs('$("#modalPhoto").on("hidden.bs.modal",
function () {
     $(this).removeData();
})');

?>
<style>
    .grid-view td {
        white-space: pre-line;
    }
</style>
<div class="modal remote fade" id="modalUser">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
<div class="modal remote fade" id="modalMeasure">
    <div class="modal-dialog" style="width: 700px">
        <div class="modal-content loader-lg" id="modalContentMeasure">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalDefects">
    <div class="modal-dialog" style="width: 700px">
        <div class="modal-content loader-lg" id="modalContentDefects">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalPhoto">
    <div class="modal-dialog" style="width: 800px; height: 400px">
        <div class="modal-content loader-lg" id="modalContentPhoto">
        </div>
    </div>
</div>

<div class="modal remote fade" id="modalFilter">
    <div class="modal-dialog" style="width: 400px; height: 500px">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
