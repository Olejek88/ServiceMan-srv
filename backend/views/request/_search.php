<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-request-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'comment') ?>

    <?= $form->field($model, 'userUuid') ?>

    <?= $form->field($model, 'equipmentUuid') ?>

    <?= $form->field($model, 'requestStatusUuid') ?>

    <?= $form->field($model, 'objectUuid') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
