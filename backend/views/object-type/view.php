<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model \common\models\ObjectType */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы объекта'), 'url' => ['index']
];
?>
<div class="order-verdict-view box-padding">
    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <div class="user-image-photo">
                <img src="<?php echo Html::encode($model->getImageUrl()) ?>" alt="">
            </div>

            <h3 class="text-center">
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
                                        'label' => 'Название',
                                        'value' => $model->title
                                    ],
                                    [
                                        'label' => 'Описание',
                                        'value' => $model->description
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
