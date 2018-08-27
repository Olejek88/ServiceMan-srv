<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-stage-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'comment') ?>

    <?= $form->field($model, 'taskUuid') ?>

    <?= $form->field($model, 'equipmentUuid') ?>

    <?php // echo $form->field($model, 'stageStatusUuid') ?>

    <?php // echo $form->field($model, 'stageVerdictUuid') ?>

    <?php // echo $form->field($model, 'stageTemplateUuid') ?>

    <?php // echo $form->field($model, 'startDate') ?>

    <?php // echo $form->field($model, 'endDate') ?>

    <?php // echo $form->field($model, 'flowOrder') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
