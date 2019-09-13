<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TaskTemplateEquipmentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Связь задачи с типом оборудования');
?>
<div class="task-equipment-stage-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="panel-body">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Создать связь задачи с элементом'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>

                    <h6 class="text-center">
                        <?php try {
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
                                            'label' => 'Задача',
                                            'contentOptions' => [
                                                'class' => 'table_class'
                                            ],
                                            'headerOptions' => [
                                                'class' => 'text-center'
                                            ],
                                            'value' => 'taskTemplate.title'
                                        ],
                                        [
                                            'label' => 'Элементы',
                                            'contentOptions' => [
                                                'class' => 'table_class'
                                            ],
                                            'headerOptions' => [
                                                'class' => 'text-center'
                                            ],
                                            'value' => 'equipmentType.title',
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
                                            ],
                                            'template' => '{view} {update} {link}',
                                        ],
                                    ],
                                ]
                            );
                        } catch (Exception $e) {
                        } ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
