<?php
/* @var $searchModel backend\models\ShutdownSearch */

use common\models\Contragent;
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
        'attribute' => 'contragent',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Исполнитель',
        'mergeHeader' => true,
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
                return date("Y-m-d h:m", strtotime($data->startDate));
            else
                return 'нет даты начала';
        }
    ],
    [
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
                return date("Y-m-d h:m", strtotime($data->endDate));
            else
                return 'нет даты окончания';
        }
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
            Html::a('Новая', ['/shutdown/create'], ['class' => 'btn btn-success'])
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
