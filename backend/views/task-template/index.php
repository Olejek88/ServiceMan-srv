<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $searchModel  backend\models\TaskSearchTemplate */

$this->title = Yii::t('app', 'Шаблоны задач');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 400px; margin: 0 auto;">
                <li class=""><a href="/task">Список</a></li>
                <li class=""><a href="/task-type">Тип</a></li>
                <li class=""><a href="/task-status">Статусы</a></li>
                <li class=""><a href="/task-verdict">Вердикты</a></li>
                <li class="active"><a href="/task-template">Шаблоны</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php
                        echo Html::a(
                            Yii::t('app', 'Создать'),
                            ['create'], ['class' => 'btn btn-success']
                        );
                        ?>
                    </p>

                    <h6 class="text-center">
                        <?php
                        echo GridView::widget(
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
                                            'style' => 'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content' => function ($data) {
                                            return $data->_id;
                                        }
                                    ],
                                    [
                                        'attribute' => 'title',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'width: 50px; padding: 5px 10px 5px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content' => function ($data) {
                                            return $data->title;
                                        }
                                    ],
                                    [
                                        'attribute' => 'taskType',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'padding: 5px 10px 5px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'value' => 'taskType.title',
                                    ],
                                    [
                                        'attribute' => 'normative',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'padding: 5px 10px 5px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content' => function ($data) {
                                            return $data->normative;
                                        }
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'headerOptions' => [
                                            'class' => 'text-center', 'width' => '70'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'text-center',
                                            'style' => 'padding: 5px 10px 5px 10px;'
                                        ],
                                        'template' => '{view} {update} {delete}{link}',
                                    ],
                                ],
                            ]
                        );
                        ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
