<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $searchModel */

$this->title = Yii::t('app', 'Журнал оборудования');
?>
<div class="equipment-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 405px; margin: 0 auto;">
                <li class=""><a href="/equipment">Список оборудования</a></li>
                <li class=""><a href="/equipment-register-type">Тип записи</a></li>
                <li class="active"><a href="/equipment-register">Журнал событий</a></li>
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
                                    'attribute' => 'registerTypeUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'registerType.title',
                                ],
                                [
                                    'attribute' => 'userUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'user.name',
                                ],
                                [
                                    'attribute' => 'equipmentUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'equipment.title',
                                ],
                                [
                                    'attribute' => 'date',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->date;
                                    }
                                ],
                                [
                                    'attribute' => 'description',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Действия',
                                    'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                    'contentOptions' => [
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
