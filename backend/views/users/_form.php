<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Users;
use yii\web\View;
use common\components\Tag;

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
        } else if($(this).val() == ' . Users::USERS_ARM_WORKER . ') {
            console.log("type both");
            $(".field-userarm-password").show();
            $("#userarm-password").trigger("change");
            $(".field-userarm-tagtype").show();
            $(".field-userarm-pin").show();
            $("#userarm-pin").trigger("change");
        }
    });
    $("#userarm-type").trigger("change");', View::POS_READY);
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
        Users::USERS_ARM_WORKER => 'Оператор/Исполнитель',
    ];

    if (Yii::$app->user->can(User::PERMISSION_ADMIN)) {
        echo $form->field($userArm, 'type')->label(Yii::t('app', 'Тип пользователя'))
            ->dropDownList($typeList);
    }
    ?>

    <?php
    echo $form->field($userArm, 'password')->passwordInput(['autofocus' => true]);
    ?>

    <?php
    $tagTypeList = [
        Tag::TAG_TYPE_PIN => 'Пинкод',
        Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
        Tag::TAG_TYPE_NFC => 'NFC метка',
        Tag::TAG_TYPE_UHF => 'UHF метка'
    ];
    echo $form->field($userArm, 'tagType')->dropDownList($tagTypeList);
    echo $form->field($userArm, 'pin')->textInput([]);
    ?>

    <?php echo $form->field($userArm, 'name')->textInput([]) ?>

    <?php echo $form->field($userArm, 'whoIs')->textInput([]) ?>

    <?php echo $form->field($userArm, 'contact')->textInput([]) ?>

    <?php
    if (Yii::$app->user->can(User::PERMISSION_ADMIN)) {
        echo $form->field($userArm, 'role')
            ->label(Yii::t('app', 'Права пользователя в системе'))
            ->dropDownList($roleList);
    }
    ?>

    <?php
    $statusList = [
        User::STATUS_DELETED => 'Заблокирован',
        User::STATUS_ACTIVE => 'Активен',
    ];
    if (Yii::$app->user->can(User::PERMISSION_ADMIN)) {
        echo $form->field($userArm, 'status')
            ->label(Yii::t('app', 'Состояние пользователя'))
            ->dropDownList($statusList);
    }
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(!$model->isNewRecord ? 'Обновить' : 'Создать', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>