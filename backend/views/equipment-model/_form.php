<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\EquipmentType;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-model-form">

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
    $types = EquipmentType::find()->orderBy('title')->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    echo $form->field($model, 'equipmentTypeUuid',
        ['template'=>'{label}<div class="input-group">{input}<span class="input-group-btn" style="padding-left: 5px">
        <a href="/equipment-type/create">
        <button class="btn btn-success" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        </button></a></span></div>{hint}{error}'])
        ->widget(Select2::classname(),
        [
        'data' => $items,
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Выберите тип..',
            'style' => ['height' => '42px', 'padding-top' => '10px']
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    unset($items);
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'image')->widget(
        FileInput::classname(),
        ['options' => ['accept' => '*'],]
    ); ?>

    <div class="form-group text-center">

        <?php echo Html::submitButton(
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
