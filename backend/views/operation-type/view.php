<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OperationType */
/* @var $parentType string */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы операции'),
    'url' => ['index']
];
?>
<div class="operation-type-view box-padding">

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
                        echo Html::a(Yii::t('app', 'Список'),
                            ['index'],
                            ['class' => 'btn btn-info']);
                        ?>
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
                        <?php
                        echo Html::a(Yii::t('app', 'Добавить'),
                            ['create'],
                            ['class' => 'btn btn-success']);
                        ?>

                    </p>
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    '_id',
                                    'uuid',
                                    [
                                        'label' => 'Родитель',
                                        'value' => $parentType,
                                    ],
                                    'title',
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
