<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $searchModel backend\models\EquipmentSearchModel */

$this->title = Yii::t('app', 'Модели оборудования');
?>
<div class="equipment-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 450px; margin: 0 auto;">
                <li class=""><a href="/equipment">Список</a></li>
                <li class=""><a href="/equipment-type">Тип</a></li>
                <li class="active"><a href="/equipment-model">Модель</a></li>
                <li class=""><a href="/equipment-status">Статус</a></li>
                <li class=""><a href="/equipment-model/tree">Дерево моделей</a></li>
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
                                            'style' => 'width: 50px; text-align: center'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'content' => function ($data) {
                                            return $data->_id;
                                        }
                                    ],
                                    [
                                        'attribute' => 'uuid',
                                        'contentOptions' => [
                                            'class' => 'table_class'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'content' => function ($data) {
                                            return $data->uuid;
                                        }
                                    ],
                                    [
                                        'attribute' => 'title',
                                        'contentOptions' => [
                                            'class' => 'table_class'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'content' => function ($data) {
                                            return $data->title;
                                        }
                                    ],
                                    [
                                        'attribute' => 'equipmentType',
                                        'contentOptions' => [
                                            'class' => 'table_class'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'equipmentType.title',
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
