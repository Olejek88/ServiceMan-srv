<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OperationTool */
/* @var $tool common\models\Tool */
/* @var $operationTemplate common\models\OperationTemplate */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Инструмент операций'),
    'url' => ['index']
];
?>
<div class="operation-tool-view box-padding">

    <div class="panel panel-default">
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
                            Yii::t('app', 'Обновить'),
                            ['update', 'id' => $model->_id],
                            ['class' => 'btn btn-primary']
                        ) ?>
                        <?php
                        $msg = 'Вы действительно хотите удалить данный элемент?';
                        echo Html::a(
                            Yii::t('app', 'Удалить'),
                            ['delete', 'id' => $model->_id],
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
                                    [
                                        'label' => '_id',
                                        'value' => $model->_id
                                    ],
                                    [
                                        'label' => 'Uuid',
                                        'value' => $model->uuid
                                    ],
                                    [
                                        'label' => 'Шаблон',
                                        'value' => $operationTemplate['title']
                                    ],
                                    [
                                        'label' => 'Инструмент',
                                        'value' => $tool['title']
                                    ],
                                    [
                                        'label' => 'Количество',
                                        'value' => $model->quantity
                                    ],
                                    [
                                        'label' => 'Создан',
                                        'value' => $model->createdAt
                                    ],
                                    [
                                        'label' => 'Изменен',
                                        'value' => $model->changedAt
                                    ],

                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
