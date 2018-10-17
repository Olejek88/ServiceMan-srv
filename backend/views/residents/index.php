<?php
/* @var $searchModel backend\models\ResidentSearch */

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Таблица абонентов');
$gridColumns = [
    [
        'attribute' => '_id',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return Html::a($data->_id,
                '/residents/timeline?id=' . $data->_id);
        }
    ],
    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('resident-details', ['model' => $model]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'owner',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->owner;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'inn',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->inn;
        }
    ],
    [
        'attribute' => 'flat',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'header' => 'Квартира ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/flat/create?from=residents/table', [
                    'title' => Yii::t('app', 'Добавить')]),
        'content' => function ($data) {
            return 'ул.' . $data['flat']['house']['street']->title . ', ' .
                $data['flat']['house']->number . ', ' .
                $data['flat']->number;
        }
    ],
    [
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->changedAt) > 0)
                return date("Y-m-d h:m", strtotime($data->changedAt));
            else
                return 'не открыт';
        }
    ],
    /*    [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'authorUuid',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'header' => 'Автор '.Html::a('<span class="glyphicon glyphicon-plus"></span>',
                    '/users/create?from=orders/table', [
                    'title' => Yii::t('app', 'Добавить')]),
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'value' => 'author.name',
            'editableOptions'=> function ($model, $key, $index, $widget) {
                $users = ArrayHelper::map(Users::find()->orderBy('name')->all(), 'uuid', 'name');
                return [
                    'header' => 'Автор наряда',
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
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Любой'],
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'userUuid',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'header' => 'Исполнитель',
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'editableOptions'=> function ($model, $key, $index, $widget) {
                $users = ArrayHelper::map(Users::find()->orderBy('name')->all(), 'uuid', 'name');
                return [
                    'header' => 'Исполнитель наряда',
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
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'orderStatusUuid',
            'header' => 'Статус '.Html::a('<span class="glyphicon glyphicon-plus"></span>',
                    '/order-status/create?from=orders/table', ['title' => Yii::t('app', 'Добавить')]),
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'vAlign' => 'middle',
            'editableOptions'=> function () {
                $status=[];
                $list=[];
                $statuses = OrderStatus::find()->orderBy('title')->all();
                foreach ($statuses as $stat) {
                    $color='background-color: white';
                    if ($stat['uuid']==OrderStatus::CANCELED ||
                        $stat['uuid']==OrderStatus::NEW_ORDER)
                        $color='background-color: gray';
                    if ($stat['uuid']==OrderStatus::IN_WORK)
                        $color='background-color: yellow';
                    if ($stat['uuid']==OrderStatus::UN_COMPLETE)
                        $color='background-color: lightred';
                    if ($stat['uuid']==OrderStatus::COMPLETE)
                        $color='background-color: green';
                    $list[$stat['uuid']] = $stat['title'];
                    $status[$stat['uuid']] = "<span class='badge' style='".$color."; height: 12px; margin-top: -3px'> </span>&nbsp;".
                        $stat['title'];
                }
                return [
                    'header' => 'Статус наряда',
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'displayValueConfig' => $status,
                    'data' => $list
                ];
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(OrderStatus::find()->orderBy('title')->all(),
                'uuid', 'title'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Любой'],
            'format' => 'raw'
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'orderVerdictUuid',
            'headerOptions' => ['class' => 'text-center'],
            'header' => 'Вердикт '.Html::a('<span class="glyphicon glyphicon-plus"></span>',
                    '/order-verdict/create?from=orders/table', ['title' => Yii::t('app', 'Добавить')]),
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'vAlign' => 'middle',
            'editableOptions'=> function () {
                $status=[];
                $list=[];
                $statuses = OrderVerdict::find()->orderBy('title')->all();
                foreach ($statuses as $stat) {
                    $color='background-color: gray';
                    if ($stat['uuid']==OrderVerdict::CANCELED ||
                        $stat['uuid']==OrderVerdict::UNKNOWN)
                        $color='background-color: gray';
                    if ($stat['uuid']==OrderVerdict::UN_COMPLETE)
                        $color='background-color: lightred';
                    if ($stat['uuid']==OrderVerdict::COMPLETE)
                        $color='background-color: green';
                    $list[$stat['uuid']] = $stat['title'];
                    $status[$stat['uuid']] = "<span class='badge' style='".$color."; height: 12px; margin-top: -3px'> </span>&nbsp;".
                        $stat['title'];
                }
                return [
                    'header' => 'Вердикт наряда',
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'displayValueConfig' => $status,
                    'data' => $list
                ];
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(OrderVerdict::find()->orderBy('title')->all(),
                'uuid', 'title'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true]
            ],
            'filterInputOptions' => ['placeholder' => 'Любой'],
            'format' => 'raw'
        ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'header' => 'Действия',
    ]
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
            Html::a('Новый', ['/residents/create'], ['class' => 'btn btn-success']) . ' ' .
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/residents/table'], ['data-pjax' => 0,
                'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}'
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'residents'
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
    'floatHeader' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Абоненты',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
