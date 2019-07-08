<?php

use common\components\MainFunctions;
use common\models\EquipmentSystem;
use common\models\Users;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserSystem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-status-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'id' => 'form'
        ],
    ]);
    ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить специализацию</h4>
    </div>
    <div class="modal-body">

        <?php
        echo $form->field($model, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
        echo $form->field($model, 'userUuid')
            ->hiddenInput(['value' => $model['userUuid']])
            ->label(false);
        echo Html::hiddenInput("uuid", "123");

        echo $form->field($model, 'oid')->hiddenInput(['value' => Users::ORGANISATION_UUID])->label(false);

        $equipmentSystem = EquipmentSystem::find()->all();
        $items = ArrayHelper::map($equipmentSystem, 'uuid', 'titleUser');
        echo $form->field($model, 'equipmentSystemUuid')->widget(Select2::class,
            [
                'name' => 'equipmentSystemUuid',
                'language' => 'ru',
                'data' => $items,
                'options' => ['placeholder' => 'Выберите специализацию ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false);
        ?>

        <div class="form-group text-center">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Сменить') : Yii::t('app', 'Сменить'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]) ?>
        </div>
        <script>
            $(document).on("beforeSubmit", "#dynamic-form", function () {
            }).on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "add-system",
                    type: "post",
                    data: $('form').serialize(),
                    success: function () {
                        $('#modalAddSystem').modal('hide');
                    },
                    error: function () {
                    }
                })
            });
        </script>

        <?php ActiveForm::end(); ?>
    </div>

</div>
