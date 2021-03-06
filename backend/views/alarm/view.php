<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model common\models\Alarm */

$this->title = "Предупреждение / авария / событие";
?>
<div class="order-status-view box-padding">

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
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </p>
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    'latitude',
                                    'longitude',
                                    'uuid',
                                    [
                                        'label' => 'Пользователь',
                                        'value' => $model['user']->name
                                    ],
                                    [
                                        'label' => 'Тип',
                                        'value' => $model['alarmType']->title
                                    ],
                                    [
                                        'label' => 'Статус',
                                        'value' => $model['alarmStatus']->title
                                    ],
                                    [
                                        'label' => 'Объект',
                                        'value' => $model['object']->title
                                    ],
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
