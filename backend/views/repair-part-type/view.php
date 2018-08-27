<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RepairPartType */
/* @var $parentType string */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы запчастей'),
    'url' => ['index']
];
?>
<div class="repair-part-type-view box-padding">

    <div class="box box-default">
        <div class="box-header with-border">
            <h2><?php echo Html::encode($this->title) ?></h2>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="box-body" style="padding: 30px;">
            <p>
                <?php
                echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                    ['model' => $model]);
                ?>
            </p>

            <?php
            echo DetailView::widget(
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
            );
            ?>
        </div>
    </div>

</div>
