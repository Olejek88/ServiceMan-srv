<?php

use common\models\EquipmentType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\DocumentationType;
use common\models\Equipment;
use common\models\EquipmentModel;
use app\commands\MainFunctions;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Documentation */
/* @var $form yii\widgets\ActiveForm */
/* @var $entityType */
?>

<div class="documentation-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-documentation',
            'options' => [
                'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                'enctype' => 'multipart/form-data'
            ],
        ]
    );
    ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    $items = [
        'e' => 'Оборудование',
        'm' => 'Модель оборудования'
    ];
    $opt = [
        'inline' => true,
        'onchange' => '
        // удаляем элементы из списка на проверку
        $("#form-input-documentation")
            .yiiActiveForm("remove", "documentation-equipmenttypeuuid");
        $("#form-input-documentation")
            .yiiActiveForm("remove", "documentation-equipmentuuid");

        if ($("input:checked", this).val() == "e") {
            // скрываем элемент формы тип оборудования
            $(".field-documentation-equipmenttypeuuid").hide();

            // Показываем элемент формы Оборудование
            $(".field-documentation-equipmentuuid").show();
            // добавляем его в список на проверку
            $("#form-input-documentation").yiiActiveForm("add", {
                id: "documentation-equipmentuuid",
                name: "Documentation[equipmentUuid]",
                container: ".field-documentation-equipmentuuid",
                input: "#documentation-equipmentuuid",
                error: ".help-block",  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Validation Message Here"});
                }
            }); 
        } else {
            // скрываем элемент формы Оборудование
            $(".field-documentation-equipmentuuid").hide();
            
            // Показываем элемент формы тип оборудования
            $(".field-documentation-equipmenttypeuuid").show();
            // добавляем его в список на проверку
            $("#form-input-documentation").yiiActiveForm("add", {
                id: "documentation-equipmenttypeuuid",
                name: "Documentation[equipmentModelUuid]",
                container: ".field-documentation-equipmenttypeuuid",
                input: "#documentation-equipmenttypeuuid",
                error: ".help-block",  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Validation Message Here"});
                }
            }); 
        }
        '
    ];
    echo $form->field($entityType, 'entityType')->radioList($items, $opt)
        ->label('Документация относится к');
    $this->registerJs(
        '
        jQuery(document).ready(function() {
            $("#dynamicmodel-entitytype").change();
        });
        '
    );
    ?>

    <?php
    $equipment = Equipment::find()->orderBy(['changedAt' => SORT_DESC])->all();
    $items = ['' => 'нет'];
    $items += ArrayHelper::map($equipment, 'uuid', 'title');
    echo $form->field($model, 'equipmentUuid')->dropDownList($items);
    ?>

    <?php
    $equipmentType = EquipmentType::find()
        ->orderBy(['changedAt' => SORT_DESC])->all();
    $items = ['' => 'нет'];
    $items += ArrayHelper::map($equipmentType, 'uuid', 'title');
    echo $form->field($model, 'equipmentTypeUuid')->dropDownList($items);
    ?>

    <?php
    $documentationType = DocumentationType::find()->orderBy('title')->all();
    $items = ArrayHelper::map($documentationType, 'uuid', 'title');
    echo $form->field($model, 'documentationTypeUuid')->dropDownList($items);
    ?>

    <?php
    echo $form->field($model, 'path')
        ->widget(FileInput::class, ['options' => ['accept' => '*'],]);
    ?>

    <div class="form-group text-center">

        <?php
        echo Html::submitButton(
            $model->isNewRecord
                ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'),
            [
                'class' => $model->isNewRecord
                    ? 'btn btn-success' : 'btn btn-primary'
            ]
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
