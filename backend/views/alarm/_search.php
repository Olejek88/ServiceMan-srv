<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AlarmSearch */
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

    <?php echo $form->field($model, 'alarmTypeUuid') ?>

    <?php echo $form->field($model, 'alarmStatusUuid') ?>

    <?php echo $form->field($model, 'userUuid') ?>

    <?php echo $form->field($model, 'objectUuid') ?>

    <?php echo $form->field($model, 'comment') ?>

    <?php echo $form->field($model, 'latitude') ?>

    <?php echo $form->field($model, 'longitude') ?>

    <?php echo $form->field($model, 'date') ?>

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
