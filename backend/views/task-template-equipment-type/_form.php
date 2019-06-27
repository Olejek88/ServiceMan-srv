<?php

use common\components\MainFunctions;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TaskTemplate;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\TaskTemplateEquipmentType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-equipment-stage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php
    $list = TaskTemplate::find()->all();
    $items = ArrayHelper::map($list, 'uuid', 'title');
    unset($list);
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);
    unset($items);
    ?>

    <?php
    $equipmentTypes = EquipmentType::find()->all();
    $items = ArrayHelper::map($equipmentTypes, 'uuid', function ($model) {
        return $model['title'];
    });
    echo $form->field($model, 'equipmentTypeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип оборудования..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <div class="form-group text-center">
        <?php
        if ($model->isNewRecord) {
            $buttonText = Yii::t('app', 'Создать');
            $buttonClass = 'btn btn-success';
        } else {
            $buttonText = Yii::t('app', 'Обновить');
            $buttonClass = 'btn btn-primary';
        }

        echo Html::submitButton($buttonText, ['class' => $buttonClass])
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
