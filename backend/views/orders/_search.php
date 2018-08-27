<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orders-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'authorUuid') ?>

    <?= $form->field($model, 'userUuid') ?>

    <?php // echo $form->field($model, 'recieveDate') ?>

    <?php // echo $form->field($model, 'startDate') ?>

    <?php // echo $form->field($model, 'openDate') ?>

    <?php // echo $form->field($model, 'closeDate') ?>

    <?php // echo $form->field($model, 'orderStatusUuid') ?>

    <?php // echo $form->field($model, 'orderVerdictUuid') ?>

    <?php // echo $form->field($model, 'attemptSendDate') ?>

    <?php // echo $form->field($model, 'attemptCount') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
