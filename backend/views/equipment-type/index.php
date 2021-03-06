<?php

use common\models\EquipmentTypeTree;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $model common\models\EquipmentType */
/* @var $searchModel backend\models\EquipmentSearchType */

$this->title = Yii::t('app', 'Типы элементов');
?>
<div class="order-status-view box-padding" style="width: 95%; min-height: 2482px">
    <?php
    echo $this->render('@backend/views/yii2-app/layouts/references-menu.php');
    ?>
    <div class="panel panel-default" style="float: right; width: 75%">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
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
                                        'attribute' => 'equipmentSystem.title',
                                        'contentOptions' => [
                                            'class' => 'table_class'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'header' => 'Инженерная система'
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'headerOptions' => [
                                            'class' => 'text-center', 'width' => '70'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'template' => '{view} {update} {link}',
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
