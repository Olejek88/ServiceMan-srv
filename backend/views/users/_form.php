<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $userArm backend\models\UserArm */
/* @var $form yii\widgets\ActiveForm */
/* @var $roleList array */
?>

<div class="user-form">

    <?php
    $action = $model->isNewRecord ? 'create' : 'update?id=' . $model->_id;
    $form = ActiveForm::begin(['id' => 'form-signup', 'action' => '/users/' . $action]);
    ?>

    <?php echo $form->field($userArm, 'username')->textInput(['readonly' => empty($userArm->username) ? false : true]) ?>

    <?php echo $form->field($userArm, 'password')->passwordInput(['autofocus' => true]) ?>

    <?php echo $form->field($userArm, 'pin')->textInput([]) ?>

    <?php echo $form->field($userArm, 'name')->textInput([]) ?>

    <?php echo $form->field($userArm, 'whoIs')->textInput([]) ?>

    <?php echo $form->field($userArm, 'contact')->textInput([]) ?>

    <?php echo $form->field($userArm, 'role')
        ->label(Yii::t('app', 'Права пользователя в системе'))
        ->dropDownList($roleList);
    ?>

    <?php
    $statusList = [
        User::STATUS_DELETED => 'Заблокирован',
        User::STATUS_ACTIVE => 'Активен',
    ];
    echo $form->field($userArm, 'status')
        ->label(Yii::t('app', 'Состояние пользователя'))
        ->dropDownList($statusList);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(!$model->isNewRecord ? 'Обновить' : 'Создать', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>