<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TaskOperationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-operation-search box-padding">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?php echo $form->field($model, '_id') ?>

    <?php echo $form->field($model, 'uuid') ?>

    <?php echo $form->field($model, 'taskTemplateUuid') ?>

    <?php echo $form->field($model, 'operationTemplateUuid') ?>

    <?php echo $form->field($model, 'createdAt') ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('app', 'Search'), ['class' => 'btn btn-primary']
        ) ?>
        <?php echo Html::resetButton(
            Yii::t('app', 'Reset'), ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
