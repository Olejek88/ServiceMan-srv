<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserChannelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-channel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'messageChannelUuid') ?>

    <?= $form->field($model, 'messageTypeUuid') ?>

    <?= $form->field($model, 'userUuid') ?>

    <?= $form->field($model, 'channelId') ?>

    <?= $form->field($model, 'active') ?>

    <?= $form->field($model, 'createdAt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
