<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentAttribute */

$this->title = 'Аттрибут для оборудования';
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Аттрибуты оборудования'), 'url' => ['index']
];
?>
<div class="order-status-view box-padding">
    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">
            <h1 class="text-center"></h1>

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Обновить'),
                            ['update', 'id' => $model->_id],
                            ['class' => 'btn btn-primary']
                        ) ?>
                        <?php
                        $msg = 'Вы действительно хотите удалить данный элемент?';
                        echo Html::a(
                            Yii::t('app', 'Удалить'),
                            ['delete', 'id' => $model->_id
                            ],
                            [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', $msg),
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>
                    </p>
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    '_id',
                                    'uuid',
                                    [
                                        'label' => 'Оборудование',
                                        'value' => $model['equipment']->title
                                    ],
                                    [
                                        'label' => 'Тип аттрибута',
                                        'value' => $model['attributeType']->name
                                    ],
                                    'value',
                                    'date',
                                    'createdAt',
                                    'changedAt',
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
