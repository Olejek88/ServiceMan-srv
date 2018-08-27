<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользовательские каналы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 200px; margin: 0 auto;">
                <li class=""><a href="/users">Пользователи</a></li>
                <li class="active"><a href="/message-channel">Каналы</a></li>
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
                                    'attribute' => 'messageChannelUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'messageChannel.title',
                                ],
                                [
                                    'attribute' => 'messageTypeUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'messageType.title',
                                ],
                                [
                                    'attribute' => 'userUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'user.name',
                                ],

                                [
                                    'attribute' => 'channelId',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->channelId;
                                    }
                                ],
                                [
                                    'attribute' => 'active',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->active;
                                    }
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Действия',
                                    'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                    'contentOptions' => [
                                        'class' => 'text-center',
                                        'style' => 'padding: 5px 10px 5px 10px;'
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
