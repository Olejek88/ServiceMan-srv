<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\UserArm */
/* @var $form yii\widgets\ActiveForm */
/* @var $roleList array */
?>

<div class="user-form">

    <?php
    $action = empty($model->_id) ? 'create' : 'update?id=' . $model->_id;
    $form = ActiveForm::begin(['id' => 'form-signup', 'action' => '/users/' . $action]);
    ?>

    <?php echo $form->field($model, 'username')->textInput(['readonly' => empty($model->username) ? false : true]) ?>

    <?php echo $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

    <?php echo $form->field($model, 'pin')->textInput([]) ?>

    <?php echo $form->field($model, 'name')->textInput([]) ?>

    <?php echo $form->field($model, 'whoIs')->textInput([]) ?>

    <?php echo $form->field($model, 'contact')->textInput([]) ?>

    <?php echo $form->field($model, 'role')
        ->label(Yii::t('app', 'Права пользователя в системе'))
        ->dropDownList($roleList);
    ?>

    <?php
    $statusList = [
        User::STATUS_DELETED => 'Заблокирован',
        User::STATUS_ACTIVE => 'Активен',
    ];
    echo $form->field($model, 'status')
        ->label(Yii::t('app', 'Состояние пользователя'))
        ->dropDownList($statusList);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(!empty($model->_id) ? 'Обновить' : 'Создать', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>