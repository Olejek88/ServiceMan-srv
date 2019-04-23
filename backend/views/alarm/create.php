<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $model \common\models\Alarm */

$this->title = Yii::t('app', 'Создать предупреждение');
?>
<div class="equipment-create box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            Примечание: Вы не должны пользоваться этой формой. Она имеет только техническое применение.
            Предупреждения создаются на мобильном клиенте и в дереве объектов.
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <?php echo $this->render(
                        '_form',
                        [
                            'model' => $model,
                        ]
                    ) ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
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
                                'attribute' => 'user',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'value' => 'user.name',
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
                                'attribute' => 'alarmType',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'value' => 'alarmType.title',
                            ]
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
