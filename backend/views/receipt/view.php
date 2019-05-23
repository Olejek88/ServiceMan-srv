<?php

use common\models\Receipt;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model Receipt */

$this->title = "Записи приема граждан";
?>
<div class="task-request-view box-padding">

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
                                    [
                                        'label' => '_id',
                                        'value' => $model->_id
                                    ],
                                    [
                                        'label' => 'Контрагент',
                                        'value' => $model['contragent']['title']
                                    ],
                                    [
                                        'label' => 'Оператор',
                                        'value' => $model['user']['name']
                                    ],
                                    [
                                        'label' => 'Заявка',
                                        'value' => $model['request']['title']
                                    ],
                                    [
                                        'label' => 'Описание',
                                        'value' => $model->description
                                    ],
                                    [
                                        'label' => 'Результат',
                                        'value' => $model->result
                                    ],
                                    [
                                        'label' => 'Статус',
                                        'value' => $model->closed
                                    ]
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
