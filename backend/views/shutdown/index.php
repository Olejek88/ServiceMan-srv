<?php
/* @var $searchModel backend\models\ShutdownSearch */

use common\models\Contragent;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Управление аварийными отключениями');

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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'contragentUuid',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Исполнитель',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'value' => 'contragent.title',
        'editableOptions' => function () {
            $contragents = ArrayHelper::map(Contragent::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $contragents,
                'data' => $contragents
            ];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Contragent::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'startDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->startDate)>0)
                return date("d-m-Y h:m", strtotime($data->startDate));
            else
                return 'нет даты начала';
        },
        'editableOptions' => [
            'header' => 'Дата начала',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' => 'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'dd-MM-yyyy hh:mm',
                'saveFormat' => 'php:Y-m-d h:m',
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
        'attribute' => 'endDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->endDate)>0)
                return date("d-m-Y h:m", strtotime($data->endDate));
            else
                return 'нет даты окончания';
        },
        'editableOptions' => [
            'header' => 'Дата окончания',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'dd-MM-yyyy hh:mm',
                'saveFormat' => 'php:Y-m-d h:m',
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
        'content' => function ($data) {
            return date("d-m-Y h:m", strtotime($data->changedAt));
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
            Html::a('Новое отключение',
                ['/shutdown/form'],
                [
                    'title' => 'Добавить заявку',
                    'class' => 'btn btn-success',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_shutdown',
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
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Аварийные отключения',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
$this->registerJs('$("#modal_shutdown").on("hidden.bs.modal",
function () {
     window.location.replace("index");
})');

echo '<div class="modal remote fade" id="modal_shutdown">
            <div class="modal-dialog" style="width: 600px; height: 300px">
                <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
                </div>
            </div>
    </div>';
