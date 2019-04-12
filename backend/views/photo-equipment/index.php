<?php
/* @var $searchModel backend\models\PhotoSearch */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Фотографии оборудования');
?>
<div class="equipment-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
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
                                        'style' => 'width: 50px; text-align: center'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->_id;
                                    }
                                ],
                                [
                                    'attribute' => 'equipmentUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'hAlign' => 'center',
                                        'style' => 'width: 200px'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data['equipment']['equipmentType']->title . ' [' .
                                            $data['equipment']['flat']['house']['street']->title . ', ' .
                                            $data['equipment']['flat']['house']->number . ', ' .
                                            $data['equipment']['flat']['number'] . ']';

                                    }
                                ],
                                [
                                    'attribute' => 'userUuid',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'width: 200px'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'user.name'
                                ],
                                [
                                    'attribute' => 'latitude',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'width: 100px'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'latitude'
                                ],
                                [
                                    'attribute' => 'longitude',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'width: 100px'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value' => 'longitude'
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Действия',
                                    'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                    'contentOptions' => [
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
