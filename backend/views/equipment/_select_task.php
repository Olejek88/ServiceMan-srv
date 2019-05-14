<?php

use common\models\EquipmentStage;
use common\models\StageOperation;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
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

    <?php
    echo $form->field($model, '_id')->hiddenInput(['value' => $model["_id"]])->label(false);

    $equipmentStages = EquipmentStage::find()
        ->select('equipment_stage.*')
        ->where(['equipmentModelUuid' => $model['equipmentModelUuid']])
        ->joinWith(['stageOperation so'])
        ->groupBy(['stageTemplateUuid'])
        ->all();
    $stageOperationCount = 0;
    $allStageOperations = [];
    foreach ($equipmentStages as $equipmentStage) {
        $stageOperations = StageOperation::find()
            ->where(['stageTemplateUuid' => $equipmentStage["stageOperation"]["stageTemplate"]["uuid"]])
            ->all();
        foreach ($stageOperations as $stageOperation) {
            $allStageOperations[$stageOperationCount] = $stageOperation;
            $stageOperationCount++;
        }
    }
    $items = ArrayHelper::map($allStageOperations, 'stageTemplateUuid', 'stageTemplate.title');
    echo $form->field($model, 'equipmentStatusUuid')->widget(Select2::class,
        [
            'name' => 'status',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите задачу ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Создать задачу'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>
    <script>
        $(document).on("beforeSubmit", "#dynamic-form", function () {
        }).on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "select-task",
                type: "post",
                data: $('form').serialize(),
                success: function () {
                    $('#modalAddTask').modal('hide');
                },
                error: function () {
                }
            })
        });
    </script>

    <?php ActiveForm::end(); ?>

</div>
