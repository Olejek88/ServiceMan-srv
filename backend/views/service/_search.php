<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ServiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-search box-padding">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?php echo $form->field($model, '_id') ?>

    <?php echo $form->field($model, 'uuid') ?>

    <?php echo $form->field($model, 'service_name') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'status') ?>

    <?php echo $form->field($model, 'delay') ?>

    <?php echo $form->field($model, 'active') ?>

    <?php echo $form->field($model, 'last_start_date') ?>

    <?php echo $form->field($model, 'last_stop_date') ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('app', 'Search'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?php
        echo Html::resetButton(
            Yii::t('app', 'Reset'),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
