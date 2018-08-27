<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\EquipmentModel;
use common\models\EquipmentStatus;
use common\models\CriticalType;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use common\models\Objects;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-documentation',
            'options' => [
                'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                'enctype' => 'multipart/form-data'
            ],
        ]
    ); ?>

    <?php

    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')
            ->textInput(
                ['maxlength' => true, 'readonly' => true, 'value' => (new MainFunctions)->GUID()]
            );
    }

    ?>

    <?php

    $equipmentModel = EquipmentModel::find()->all();
    $items = ArrayHelper::map($equipmentModel, 'uuid', 'title');
    echo $form->field($model, 'equipmentModelUuid',
        ['template'=>"{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"/equipment-model/create\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}"])
        ->dropDownList($items);
    ?>

    <?php

    $equipmentStatus = EquipmentStatus::find()->all();
    $items = ArrayHelper::map($equipmentStatus, 'uuid', 'title');
    echo $form->field($model, 'equipmentStatusUuid',
        ['template'=>"{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"/equipment-status/create\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}"])
        ->dropDownList($items);
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px">
        <p style="width: 300px; margin-bottom: 0;">Дата ввода в эксплуатацию</p>
        <?php echo DateTimePicker::widget(
            [
                'model' => $model,
                'attribute' => 'startDate',
                'language' => 'ru',
                'size' => 'ms',
                'clientOptions' => [
                    'autoclose' => true,
                    'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                    'todayBtn' => true
                ]
            ]
        );
        ?>
    </div>

    <?php

    $criticalType = CriticalType::find()->all();
    $items = ArrayHelper::map($criticalType, 'uuid', 'title');
    echo $form->field($model, 'criticalTypeUuid',
        ['template'=>"{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"/critical-type/create\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}"])
        ->dropDownList($items);
    ?>

    <?php echo $form->field($model, 'tagId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'image')->widget(
        FileInput::classname(),
        ['options' => ['accept' => '*'],]
    ); ?>

    <?php
    echo $form->field($model, 'inventoryNumber')->textInput(['maxlength' => true]);
    ?>

    <?php
    echo $form->field($model, 'serialNumber')->textInput(['maxlength' => true]);
    ?>

    <?php

    $objectType = Objects::find()->all();
    $items = ArrayHelper::map($objectType, 'uuid', 'title');
    $countItems = count($items);
    $isItems = $countItems != 0;

    if ($isItems) {
        echo $form->field($model, 'locationUuid',
            ['template'=>"{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
            <a href=\"/objects/create\">
            <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
            </button></a></span></div>\n{hint}\n{error}"])
            ->dropDownList($items);
    } else {
        echo $form->field($model, 'locationUuid')->dropDownList(
            [
                '00000000-0000-0000-0000-000000000004' => 'Данных нет'
            ]
        );
    }

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

        echo Html::submitButton($buttonText, ['class' => $buttonClass]);
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
