<?php

use common\components\MainFunctions;
use common\models\Operation;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $model */

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
        'attribute' => 'title',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['operationTemplate']['title'];
        }
    ],
    [
        'attribute' => 'workStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model) {
            $status =MainFunctions::getColorLabelByStatus($model['workStatusUuid'],'task_status');
            return $status;
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->changedAt;
        }
    ],
];

$operations = Operation::find()->where(['taskUuid' => $model['uuid']]);
$provider = new ActiveDataProvider(
    [
        'query' => $operations,
        'sort' =>false,
    ]
);

echo GridView::widget(
    [
        'dataProvider' => $provider,
        'columns' => $gridColumns,
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'containerOptions' => ['style' => 'overflow: auto'],
        'beforeHeader' => [
            '{toggleData}'
        ],
        'toolbar' => [
            []
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
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<i class="glyphicon glyphicons-spade"></i>&nbsp; Операции',
            'headingOptions' => ['style' => 'background: #337ab7']
        ],
    ]
);
