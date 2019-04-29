<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \common\models\OperationFile;

/* @var $searchModel \backend\models\OperationSearch */

$this->title = Yii::t('app', 'Операция');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            <ul class="nav nav-tabs" style="width: 285px; margin: 0 auto;">
                <li class="active"><a href="/operation">Список</a></li>
                <li class=""><a href="/work-status">Статусы</a></li>
                <li class=""><a href="/operation-template">Шаблоны</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php echo Html::a(Yii::t('app', 'Создать'), ['generate'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <h6 class="text-center">
                        <?php echo GridView::widget(
                            [
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'tableOptions' => [
                                    'class' => 'table-striped table table-bordered table-hover table-condensed'
                                ],
                                'columns' => [
                                    [
                                        'attribute' => '_id',
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
                                        'attribute' => 'task',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'width: 50px'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content' => function ($data) {
                                            return $data->task->_id;
                                        }
                                    ],
                                    [
                                        'attribute' => 'operationTemplateUuid',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'value' => 'operationTemplate.title',
                                    ],
                                    [
                                        'attribute' => 'workStatusUuid',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'value' => 'workStatus.title',
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                        'contentOptions' => [
                                            'class' => 'text-center',
                                        ],
                                        'template' => '{view} {update} {delete} {link} {photo}',
                                    ],
                                ],
                            ]
                        ); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
