<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserHouse */

$this->title = 'Пользователи и дома';
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Пользователи и дома'), 'url' => ['index']
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
                                    '_id',
                                    'uuid',
                                    [
                                        'label' => 'Пользователь',
                                        'value' => $model['user']->name
                                    ],
                                    [
                                        'label' => 'Дом',
                                        'value' => $model['house']->number
                                    ],
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
