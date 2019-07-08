<?php
/* @var $searchModel backend\models\RequestSearch */

use common\models\Contragent;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Request;
use common\models\WorkStatus;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Журнал личного приема');

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
                ['../receipt/form', 'uuid' => $data['uuid']],
                [
                    'title' => 'Редактировать',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAdd',
                ]
            );
        }
    ],
    [
        'attribute' => 'date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Дата приема',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'value' => function ($model) {
            return "<span class='badge' style='background-color: gray; height: 22px'>".
                date('d-m-Y H:m', strtotime($model->date))."</span>";
//            return $model->date;
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'userCheck',
        'header' => 'ФИО лица ведущего прием',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            //return $data['user']->name . '<br/> [' . $data['author']->whoIs . ']';
            return $data['userCheck'];
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'userCheckWho',
        'header' => 'Должность лица ведущего прием',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'user',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Оператор',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['user']->name . '<br/> [' . $data['user']->whoIs . ']';
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'attribute' => 'contragentUuid',
        'header' => 'Заявитель',
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
        'editableOptions' => function () {
            $models = ArrayHelper::map(Contragent::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'header' => 'Контрагент',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Заявка',
        'mergeHeader' => true,
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'value' => function ($model) {
            if ($model['requestUuid']) {
                $request = Request::find()->where(['uuid' => $model['requestUuid']])->one();
                if ($request) {
                    $request_title = 'Заявка №' . $request['_id'] . ' ';
                    if ($request['requestStatusUuid'] == WorkStatus::COMPLETE)
                        $request_title .= "<span class='badge' style='background-color: green; height: 22px'>Выполнена</span>";
                    else
                        $request_title .= "<span class='badge' style='background-color: grey; height: 22px'>" . $request['requestStatus']->title . "</span>";
                    $request_title .= '<br/>' . $request['requestType']['title'];
                    return Html::a($request_title,
                        ['../request/form', 'uuid' => $model['requestUuid']],
                        [
                            'title' => 'Редактировать заявку',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalRequest',
                        ]
                    );
                }
            }
            $request_title = "<span class='badge' style='background-color: lightgrey; height: 22px'>не создавалась</span>";
            return Html::a($request_title,
                ['../request/form', 'receiptUuid' => $model['uuid']],
                [
                    'title' => 'Добавить заявку',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalRequest',
                ]
            );
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'description',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Описание',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Дата приема',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'content' => function ($data) {
            if (strtotime($data->date))
                return date("d-m-Y h:m", strtotime($data->date));
            else
                return 'не назначен';
        },
        'editableOptions' => [
            'header' => 'Дата назначения',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATE,
                'displayFormat' => 'dd-MM-yyyy',
                'saveFormat' => 'php:Y-m-d H:i:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ]
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'result',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Результат',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'closed',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Закрыта',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'value' => function ($model) {
            if ($model['closed'])
                return "<span class='badge' style='background-color: green; height: 22px'>Закрыта</span>";
            else
                return "<span class='badge' style='background-color: sandybrown; height: 22px'>Открыта</span>";
        },
        'editableOptions' => function () {
            $status = [false => 'Открыта', true => 'Закрыта'];
            return [
                'header' => 'Статус',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $status
            ];
        },
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'buttons' => [
            'edit' => function ($url, $model) {
                return Html::a('<span class="fa fa-edit"></span>',
                    ['../receipt/form', 'uuid' => $model['uuid']],
                    [
                        'title' => 'Редактировать заявку',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAdd',
                    ]
                );
            }
        ],
        'template' => '{edit} {delete}',
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
            Html::a('Новая запись',
                ['../receipt/form'],
                [
                    'class' => 'btn btn-success',
                    'title' => 'Добавить запись',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAdd',
                ]
            )
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
        'heading' => '<i class="fa fa-tasks"></i>&nbsp; Журнал личного приема',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
     window.location.replace("../receipt/index");
})');
$this->registerJs('$("#modalRequest").on("hidden.bs.modal",
function () {
     window.location.replace("../receipt/index");
})');
?>
<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal remote fade" id="modalRequest">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
