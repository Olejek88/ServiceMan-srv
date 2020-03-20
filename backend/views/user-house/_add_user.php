<?php

use common\models\User;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $houseUuid */
/* @var $equipmentSystemUuid */

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form'
    ],
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Назначить исполнителей</h4>
</div>
<div class="modal-body">

    <?php
    echo '<label class="control-label">Убрать исполнителя</label>';
    echo '</br>';
    $userSystems = UserSystem::find()->where(['equipmentSystemUuid' => $equipmentSystemUuid])->all();
    $userHouses = UserHouse::find()->where(['houseUuid' => $houseUuid])->all();
    $user_list = '';
    foreach ($userSystems as $userSystem) {
        foreach ($userHouses as $userHouse) {
            if ($userSystem['userUuid'] == $userHouse['userUuid']) {
                echo Html::checkbox('user-' . $userHouse['user']['_id'], false, ['label' => $userHouse['user']['name']]);
                echo '</br>';
            }
        }
    }

    echo Html::hiddenInput('houseUuid', $houseUuid);

    echo '</br>';
    echo '<label class="control-label">Добавить исполнителя</label>';
    $users = UserSystem::find()
        ->where(['equipmentSystemUuid' => $equipmentSystemUuid])
        ->joinWith('user.user')
        ->where(['user.status' => User::STATUS_ACTIVE])
        ->all();
    $items = ArrayHelper::map($users, 'user.uuid', 'user.name');
    try {
        echo Select2::widget(
            [
                'data' => $items,
                'name' => 'userAdd',
                'language' => 'ru',
                'options' => [
                    'id' => 'user-id',
                    'placeholder' => 'Выберите пользователя..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } catch (Exception $e) {
    }
    echo '</br>';

    ?>
    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Принять'), [
            'class' => 'btn btn-success'
        ]) ?>
    </div>
</div>

<script>
    $(document).on("beforeSubmit", "#dynamic-form", function () {
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "name",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modalUser').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>

<?php ActiveForm::end(); ?>
