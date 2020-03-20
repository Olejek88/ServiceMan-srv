<?php

use common\models\User;
use common\models\Users;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-status-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'id'      => 'form'
        ],
    ]);
    ?>

    <?php
    $users = Users::find()
        ->joinWith('user')
        ->andWhere(['user.status' => User::STATUS_ACTIVE])
        ->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');
        echo Select2::class,
        [
            'name' => 'user',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите пользователя ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ];
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Сменить') : Yii::t('app', 'Сменить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>
    <script>
        $(document).on("beforeSubmit", "#dynamic-form", function () {
        }).on('submit', function(e){
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

</div>
