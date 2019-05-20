<?php

use common\models\Operation;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model Operation */

$this->title = $model->_id;
?>
<div class="operation-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($model->operationTemplate['title']) ?>
            </h3>
        </div>
        <div class="panel-body">
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </p>
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
                                    'label' => 'Задача',
                                    'value' => $model->task['comment']
                                ],
                                [
                                    'label' => 'Шаблон',
                                    'value' => $model->operationTemplate['title']
                                ],
                                [
                                    'label' => 'Статус',
                                    'value' => $model->workStatus['title']
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
                </div>
            </div>
        </div>
    </div>

</div>

