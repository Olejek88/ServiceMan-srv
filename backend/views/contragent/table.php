<?php
/* @var $searchModel backend\models\SubjectSearch */

use common\models\ContragentType;
use common\models\ObjectContragent;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Таблица контрагентов');
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
            return $data->_id;
        }
    ],

    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('subject-details', ['model' => $model]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->title;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'address',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->address;
        }
    ],
    [
        'header' => 'Адрес объекта ' . Html::a('<span class="fa fa-search"></span>&nbsp',
                ['../request/search-form'],
                [
                    'title' => 'Фильтрация по адресу',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalFilter',
                ]
            ) . '&nbsp' . Html::a('<span class="fa fa-close"></span>&nbsp',
                ['../contragent']),
        'mergeHeader' => true,
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $objectContragent = ObjectContragent::find()->where(['contragentUuid' => $data['uuid']])->one();
            if ($objectContragent) {
                return $objectContragent['object']->getFullTitle();
            }
            return "";
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'phone',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->phone;
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
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->inn;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'account',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->account;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'director',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->director;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'email',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->email;
        }
    ],
    [
        'attribute' => 'contragentTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип',
        'filter' => ArrayHelper::map(ContragentType::find()->orderBy('title')->all(), 'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'format' => 'raw',
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->contragentType->title;
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'header' => 'Действия',
        'template' => '{update} {delete}'
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
            Html::a('Создать', ['/contragent/create'], ['class' => 'btn btn-success']) . ' ' .
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/contragent/table'], ['data-pjax' => 0,
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
        'heading' => '<i class="fa fa-users"></i>&nbsp; Справочник контрагентов',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

?>

<div class="modal remote fade" id="modalFilter">
    <div class="modal-dialog" style="width: 400px; height: 500px">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

