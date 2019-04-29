<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var \common\models\Documentation $model */
/* @var array $entity */

$this->title = $model->title;
?>
<div class="order-status-view box-padding">
    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            <div class="">
                <a href="<?php echo Html::encode($model->getDocUrl()) ?>">Документ</a>
            </div>

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
                                    '_id',
                                    'uuid',
                                    [
                                        'label' => $entity['label'],
                                        'value' => $entity['title']
                                    ],
                                    [
                                        'label' => 'Тип документации',
                                        'value' => $model->documentationType['title']
                                    ],
                                    'title',
                                    'createdAt',
                                    'changedAt',
                                    'path:ntext',
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
