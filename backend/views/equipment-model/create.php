<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentModel */
/* @var $searchModel backend\models\EquipmentSearchModel */

$this->title = Yii::t('app', 'Создать модель оборудования');
?>
<div class="equipment-model-create box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?php echo $this->render(
                            '_form',
                            [
                                'model' => $model,
                            ]
                        ) ?>
                    </h6>
                    <h6 class='text-center'>
                        * Если вы не нашли
                        <b><?php
                            echo Html::a(
                                'тип оборудования',
                                ['/equipment-type/create'],
                                ['target' => '_blank',]
                            ) ?></b>,
                        создайте его!
                    </h6>
                    <?php echo GridView::widget(
                        [
                            'dataProvider' => $dataProvider,
                            'tableOptions' => [
                                'class' => 'table-striped table table-bordered table-hover table-condensed'
                            ],
                            'columns' => [
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

                </div>
            </div>

        </div>
    </div>

</div>
