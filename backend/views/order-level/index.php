<?php
/* @var $searchModel backend\models\OrderSearchLevel */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $searchModel */
/* @var $dataProvider */

$this->title = Yii::t('app', 'Наряд');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 400px; margin: 0 auto;">
                <li class=""><a href="/orders">Список</a></li>
                <li class=""><a href="/order-status">Статусы</a></li>
                <li class=""><a href="/order-verdict">Вердикты</a></li>
                <li class="active"><a href="/order-level">Уровни</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Создать'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
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
                                            'style' => 'width: 50px; text-align: center; padding: 18px 10px 15px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content' => function ($data) {
                                            return $data->_id;
                                        }
                                    ],
                                    [
                                        'attribute'=>'uuid',
                                        'contentOptions' =>[
                                            'class' => 'table_class',
                                            'style'=>'padding: 18px 10px 15px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content'=>function($data){
                                            return $data->uuid;
                                        }
                                    ],
                                    [
                                        'label' => 'Иконка',
                                        'format' => 'raw',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'display:block; padding: 5px; text-align: center;'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => function ($data) {
                                            $path = 'storage/' . $data->icon;
                                            if (file_exists($path)) {
                                                $path = '/' . $path;
                                            } else {
                                                $path = '/storage/order-level/no-image-icon-4.png';
                                            }

                                            return Html::img(
                                                Url::to($path),
                                                [
                                                    'style' => 'width:50px; height: 45px; padding: 0px; border-radius: 50%; border: 1px solid #ccc;'
                                                ]
                                            );

                                        },
                                    ],
                                    [
                                        'attribute' => 'title',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'padding: 18px 10px 15px 10px;'
                                        ],
                                        'headerOptions' => ['class' => 'text-center'],
                                        'content' => function ($data) {
                                            return $data->title;
                                        }
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'headerOptions' => [
                                            'class' => 'text-center',
                                            'width' => '70'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'text-center',
                                            'style' => 'padding: 18px 10px 15px 10px;'
                                        ],
                                        'template' => '{view} {update} {delete}{link}',
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
