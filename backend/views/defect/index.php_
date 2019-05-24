<?php
/* @var $searchModel backend\models\DefectSearch */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Дефекты');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 203px; margin: 0 auto;">
                <li class="active"><a href="/defect">Список</a></li>
                <li class=""><a href="/defect-type">Тип</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?= Html::a(Yii::t('app', 'Создать'), ['create'],
                            ['class' => 'btn btn-success']) ?>
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
                                        'style'=>'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->_id;
                                    }
                                ],
                                [
                                    'attribute'=>'userUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'user.name',
                                ],
                                [
                                    'attribute'=>'defectTypeUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'defectType.title',
                                ],
                                [
                                    'attribute'=>'equipmentUuid',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'equipment.title',
                                ],
                                [
                                    'attribute'=>'comment',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->comment;
                                    }
                                ],
                            ],
                        ]); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
