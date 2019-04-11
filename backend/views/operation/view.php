<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model \common\models\Operation */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Операции'), 'url' => ['index']
];
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
                        <?php echo Html::a(
                            Yii::t('app', 'Обновить'),
                            ['update', 'id' => $model->_id],
                            ['class' => 'btn btn-primary']
                        ) ?>
                        <?php echo Html::a(
                            Yii::t('app', 'Удалить'),
                            ['delete', 'id' => $model->_id],
                            [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t(
                                        'app',
                                        'Вы действительно хотите удалить данный элемент?'
                                    ),
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
                    </h6>
                </div>
            </div>
            <?php
            foreach ($files as $file) {
                $url = Html::encode($file->getImageUrl());
                ?>
                <div style="align-content: center">
                    <img src="<?php echo $url ?>" alt="" width="300px"/>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

</div>

