<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TaskTemplateEquipmentType */

$this->title = $model->_id;
?>
<div class="task-equipment-stage-view box-padding">

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

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <div style="align-content: center">
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </div>
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
                                    'label' => 'Шаблон задачи',
                                    'value' => $model->taskTemplate->title
                                ],
                                [
                                    'label' => 'Оборудование',
                                    'value' => $model->equipmentType->title
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
