<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Role;

/* @var $this yii\web\View */
/* @var $model backend\models\UserArm */
/* @var $form yii\widgets\ActiveForm */
/* @var $role Role */
/* @var $roleList array */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

    <?php echo $form->field($model, 'login')->textInput() ?>

    <?php echo $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

    <?php echo $form->field($model, 'pin')->textInput([]) ?>

    <?php echo $form->field($model, 'name')->textInput([]) ?>

    <?php echo $form->field($model, 'whoIs')->textInput([]) ?>

    <?php echo $form->field($model, 'contact')->textInput([]) ?>

    <?php //= $form->field($model, 'userId')->textInput([]) ?>

    <?php echo $form->field($role, 'role')
        ->label(Yii::t('app', 'Права пользователя в системе'))
        ->dropDownList($roleList);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(is_numeric($model->id) ? 'Обновить' : 'Создать', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>