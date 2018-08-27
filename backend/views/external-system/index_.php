<?php
/* @var $searchModel backend\models\ExternalSystemSearch */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Внешние системы');
?>
<div class="equipment-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 265px; margin: 0 auto;">
                <li class="active"><a href="/external-system">Системы</a></li>
                <li class=""><a href="/external-tag">Теги</a></li>
                <li class=""><a href="/external-event">События</a></li>
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
                            'layout' => "{summary}\n{items}\n<div align='center'>{pager}</div>",
                            'columns' => [
                                [
                                    'attribute'=>'_id',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'width: 50px; text-align: center;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->_id;
                                    }
                                ],
                                [
                                    'attribute'=>'title',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->title;
                                    }
                                ],
                                [
                                    'attribute'=>'address',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->address;
                                    }
                                ],
                                [
                                    'attribute'=>'port',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->port;
                                    }
                                ],
                                [
                                    'attribute'=>'interface',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->interface;
                                    }
                                ],
                                [
                                    'attribute'=>'login',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->login;
                                    }
                                ],
                                [
                                    'attribute'=>'pass',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->pass;
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header'=>'Действия',
                                    'headerOptions' => ['class' => 'text-center','width' => '70'],
                                    'contentOptions' =>[
                                        'class' => 'text-center',
                                    ],
                                    'template' => '{view} {update} {delete}',
                                ],
                            ],
                        ]); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
