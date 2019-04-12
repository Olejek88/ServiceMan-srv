<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model \common\models\Request */

$this->title = $model->comment;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Заявки'),
    'url' => ['index']
];
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
                                        'label' => 'Uuid',
                                        'value' => $model->uuid
                                    ],
                                    [
                                        'label' => 'Комментарий',
                                        'value' => $model->comment
                                    ],
                                    [
                                        'label' => 'Пользователь',
                                        'value' => $model['user']['name']
                                    ],
                                    [
                                        'label' => 'Оборудование',
                                        'value' => $model['equipment']['title']
                                    ],
                                    [
                                        'label' => 'Статус',
                                        'value' => $model['requestStatus']['title']
                                    ],
                                    [
                                        'label' => 'Объект',
                                        'value' => $model['object']['title']
                                    ],
                                    [
                                        'label' => 'Дата закрытия',
                                        'value' => $model->closeDate
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
