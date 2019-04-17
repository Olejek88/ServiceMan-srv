<?php

use common\models\Equipment;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\commands\MainFunctions;
use common\models\TaskVerdict;
use common\models\WorkStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->textInput(
            ['maxlength' => true,
                'value' => (new MainFunctions)->GUID()]
        );
    }
    ?>

    <?php
    $equipment = Equipment::find()->orderBy(['changedAt' => SORT_DESC])->all();
    $items = ArrayHelper::map($equipment, 'uuid', 'title');
    echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите оборудование..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    $taskStatus = WorkStatus::find()->all();
    $items = ArrayHelper::map($taskStatus, 'uuid', 'title');
    echo $form->field($model, 'taskStatusUuid')->dropDownList($items);
    ?>

    <?php
    $taskVerdict = TaskVerdict::find()->all();
    $items = ArrayHelper::map($taskVerdict, 'uuid', 'title');
    echo $form->field($model, 'taskVerdictUuid')->dropDownList($items);
    ?>

    <?php
    echo $form->field($model, 'comment')
        ->textarea(['rows' => 4, 'style' => 'resize: none;'])
    ?>

    <div class="form-group text-center">

        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'),
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]
        );
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
