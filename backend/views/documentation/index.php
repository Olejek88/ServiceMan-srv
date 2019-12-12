<?php

use common\models\Documentation;
use common\models\DocumentationType;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $searchModel */
/* @var $dataProvider */

$this->title = Yii::t('app', 'Документация и справочники');

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
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'width' => '280px',
    ],
    [
        'attribute' => 'documentationTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => 'documentationType.title',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/documentation-type/create?from=documentation/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'filter' => ArrayHelper::map(DocumentationType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'equipmentUuid',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'content' => function ($data) {
            return $data->equipment['title'];
        },
    ],
    [
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Местоположение',
        'mergeHeader' => true,
        'content' => function ($data) {
            if ($data && $data['equipment'])
                return $data->equipment['object']->getFullTitle();
            if ($data && $data['house'])
                return $data['house']->getFullTitle();
            return "";
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => function() {
            $models = Documentation::find()->all();
            $filters =[];
            foreach ($models as $model) {
                $filters[] = [$model['uuid'] => $model->equipment->object->getFullTitle()];

            }
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
    ],
    [
        'attribute' => 'equipmentTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'width' => '180px',
        'value' => 'equipmentType.title',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'createdAt',
        'vAlign' => 'middle',
        'width' => '120px',
        'header' => 'Создан',
        'mergeHeader' => true
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'buttons'=>[
            'link' => function ($url,$model) {
                /* @var $model Documentation */
                return Html::a( '<span class="fa fa-file"></span>',
                    '/' . $model->getDocLocalPath(),
                    ['title' => $model->title, 'data-pjax' => '0']);
            }
        ],
        'template'=>'{link} {edit} {delete}'
    ]
];

echo GridView::widget([
    'id' => 'equipment-table',
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
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'documentation'
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Документация',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);


