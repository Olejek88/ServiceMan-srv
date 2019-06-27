<?php

use common\components\MainFunctions;
use common\models\TaskTemplate;use common\models\Users;use kartik\widgets\Select2;use yii\helpers\ArrayHelper;use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskTemplateEquipment */
/* @var $form yii\widgets\ActiveForm */
/* @var $equipmentTypeUuid */
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
    echo $form->field($model, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    echo $form->field($model, 'equipmentTypeUuid')->hiddenInput(['value' => $equipmentTypeUuid])->label(false);

    $taskTemplates = TaskTemplate::find()->all();
    $items = ArrayHelper::map($taskTemplates, 'uuid', 'title');
    echo Select2::widget(
        [
            'id' => 'taskTemplateUuid',
            'name' => 'taskTemplateUuid',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите шаблон ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Сменить') : Yii::t('app', 'Сменить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>
    <script>
        $(document).on("beforeSubmit", "#dynamic-form", function ($e) {
            console.log($e);
        }).on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: "../task-template-equipment/period",
                type: "post",
                data: $('form').serialize(),
                success: function ($e) {
                    console.log($e);
                    $('#modalStatus').modal('hide');
                },
                error: function () {
                }
            })
        });
    </script>

    <?php ActiveForm::end(); ?>

</div>
