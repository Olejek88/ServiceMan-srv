<?php

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
        $users = Users::find()->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');
        echo Select2::widget(
        [
            'name' => 'user',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите пользователь ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Сменить'), [
            'class' => 'btn btn-success'
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
