<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model  */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'systemUuid') ?>

    <?= $form->field($model, 'tag') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'equation') ?>

    <?= $form->field($model, 'target') ?>

    <?= $form->field($model, 'equipmentUuid') ?>

    <?= $form->field($model, 'actionTypeUuid') ?>

    <?= $form->field($model, 'taskEquipmentStageUuid') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
