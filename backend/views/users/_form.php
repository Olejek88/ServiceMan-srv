<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Users;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $userArm backend\models\UserArm */
/* @var $form yii\widgets\ActiveForm */
/* @var $roleList array */
?>

<div class="user-form">

    <?php
    $this->registerJs('
    $("#userarm-type").on("change", function() {
        if ($(this).val() == ' . Users::USERS_ARM . ') {
            console.log("type arm");
            $(".field-userarm-password").show();
            $("#userarm-pin").trigger("change");
            $(".field-userarm-pin").hide();
            $(".field-userarm-tagtype").hide();
        } else if($(this).val() == ' . Users::USERS_WORKER . ') {
            console.log("type worker");
            $(".field-userarm-tagtype").show();
            $(".field-userarm-pin").show();
            $("#userarm-password").trigger("change");
            $(".field-userarm-password").hide();
        }
    });
    $("#userarm-type").val(1).trigger("change");', View::POS_READY);
    ?>

    <?php
    $action = $model->isNewRecord ? 'create' : 'update?id=' . $model->_id;
    $form = ActiveForm::begin(['id' => 'form-signup', 'action' => '/users/' . $action]);
    ?>

    <?php echo $form->field($userArm, 'username')->textInput(['readonly' => empty($userArm->username) ? false : true]) ?>

    <?php
    $typeList = [
        Users::USERS_ARM => 'Оператор',
        Users::USERS_WORKER => 'Исполнитель',
    ];
    if ($model->isNewRecord) {
        echo $form->field($userArm, 'type')->label(Yii::t('app', 'Тип пользователя'))
            ->dropDownList($typeList);
    }
    ?>

    <?php
    if ($model->isNewRecord || (!$model->isNewRecord && $model->type == Users::USERS_ARM)) {
        echo $form->field($userArm, 'password')->passwordInput(['autofocus' => true]);
    }
    ?>

    <?php
    if ($model->isNewRecord || (!$model->isNewRecord && $model->type == Users::USERS_WORKER)) {
        $tagTypeList = [
            Users::USERS_TAG_TYPE_PIN => 'Пинкод',
            Users::USERS_TAG_TYPE_GCODE => 'QR код',
            Users::USERS_TAG_TYPE_NFC => 'NFC метка',
            Users::USERS_TAG_TYPE_UHF => 'UHF метка'
        ];
        echo $form->field($userArm, 'tagType')
            ->dropDownList($tagTypeList);
        echo $form->field($userArm, 'pin')->textInput([]);
    }
    ?>

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