<?php
/* @var $model common\models\Objects */

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Создание объекта');
?>
<div class="objects-create box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                    </h6>
                    <h6 class='text-center'>
                        * Если вы не нашли <b><?= Html::a('тип объекта', ['/object-type/create'], ['target' => '_blank',]) ?></b>, создайте сами!
                    </h6>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
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
                                'attribute'=>'title',
                                'contentOptions' =>[
                                    'class' => 'table_class'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'content'=>function($data){
                                    return $data->title;
                                }
                            ],
                            [
                                'attribute'=>'objectTypeUuid',
                                'contentOptions' =>[
                                    'class' => 'table_class'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'value'     => 'objectType.title',
                            ],
                            [
                                'attribute'=>'Координаты',
                                'contentOptions' =>[
                                    'class' => 'table_class'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'content'=>function($data){
                                    return '['.$data->latitude.','.$data->longitude.']';
                                }
                            ],
                        ],
                    ]); ?>
                </div>
            </div>

        </div>
    </div>

</div>
