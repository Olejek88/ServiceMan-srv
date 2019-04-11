<?php
/* @var $searchModel backend\models\MessageSearch */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Сообщения');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 255px; margin: 0 auto;">
                <li><a href="/users">Пользователи</a></li>
                <li><a href="/message-type">Тип</a></li>
                <li><a href="/message-channel">Канал</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?= Html::a(Yii::t('app', 'Создать'), ['create'], ['class' => 'btn btn-success']) ?>
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
                                    'attribute'=>'fromUserUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'fromUser.name',
                                ],
                                [
                                    'attribute'=>'toUserUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'toUser.name',
                                ],
                                [
                                    'attribute'=>'date',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->date;
                                    }
                                ],
                                [
                                    'attribute'=>'text',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->text;
                                    }
                                ],
                                [
                                    'attribute'=>'status',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->status;
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header'=>'Действия',
                                    'headerOptions' => ['class' => 'text-center','width' => '70'],
                                    'contentOptions' =>[
                                        'class' => 'text-center',
                                    ],
                                    'template' => '{view} {update} {delete}{link}',
                                ],
                            ],
                        ]); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
