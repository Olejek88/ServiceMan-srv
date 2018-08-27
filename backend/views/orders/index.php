<?php
/* @var $searchModel backend\models\OrderSearch */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Наряд');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 359px; margin: 0 auto;">
                <li class="active"><a href="/orders/table">Список</a></li>
                <li class=""><a href="/order-status">Статусы</a></li>
                <li class=""><a href="/order-verdict">Вердикты</a></li>
                <li class=""><a href="/order-level">Уровни</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?= Html::a(Yii::t('app', 'Создать'), ['generate'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <h6 class="text-center">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'tableOptions' => [
                                'class' => 'table-striped table table-bordered table-hover table-condensed'
                            ],
                            'columns' => [
                                [
                                    'attribute'=>'_id',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'width: 50px; text-align: center'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->_id;
                                    }
                                ],
                                [
                                    'attribute'=>'title',
                                    'contentOptions' =>[
                                        'class' => 'table_class',                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->title;
                                    }
                                ],
                                [
                                    'attribute'=>'authorUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'=> 'author.name'
                                ],
                                [
                                    'attribute'=>'userUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'=>'user.name'
                                ],
                                [
                                    'attribute'=>'orderStatusUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'orderStatus.title',
                                ],
                                [
                                    'attribute'=>'orderVerdictUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'orderVerdict.title',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header'=>'Действия',
                                    'headerOptions' => ['class' => 'text-center','width' => '70'],
                                    'contentOptions' =>[
                                        'class' => 'text-center'
                                    ],
                                    'template' => '{view} {update} {delete}',
                                    'buttons' => [
                                        'view' => function ($url,$model) {
                                            $urlExp   = explode("view?id=", $url);
                                            $urlParse = $urlExp[0].$urlExp[1];
                                            return Html::a(
                                            '<span class="glyphicon glyphicon-eye-open"></span>',
                                            $urlParse);
                                        },
                                    ],
                                ],
                            ],
                        ]); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
