<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model common\models\StageType */
/* @var $parentType string */

$this->title = 'Тип этапа: '.$model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы этапов'),
    'url' => ['index']
];
?>
<div class="stage-type-view box-padding">

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
