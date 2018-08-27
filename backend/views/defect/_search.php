<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DefectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>
    
    <?= $form->field($model, 'userUuid') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'equipmentUuid') ?>

    <?= $form->field($model, 'defectTypeUuid') ?>

    <?= $form->field($model, 'process') ?>

    <?= $form->field($model, 'comment') ?>

    <?= $form->field($model, 'taskUuid') ?>

    <?= $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
