<?php

use common\components\MainFunctions;
use common\models\Equipment;
use common\models\TaskTemplate;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskTemplateEquipment */
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
    $equipments = Equipment::find()->all();
    $items = ArrayHelper::map($equipments, 'uuid', function ($model) {
        return $model['equipmentType']['title'] . ' (' . $model['object']['house']['street']['title'] . ', ' .
            $model['object']['house']['number'] . ', ' .
            $model['object']['title'] . ')';
    });
    echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите элементы..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php echo $form->field($model, 'period')->textInput(['maxlength' => true]) ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 200px; margin-bottom: 0;">Дата первого обслуживания</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'last_date',
            'language' => 'ru',
            'size' => 'ms',
            'clientOptions' => [
                'autoclose' => true,
                'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                'todayBtn' => true
            ]
        ]);
        ?>
    </div>

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
