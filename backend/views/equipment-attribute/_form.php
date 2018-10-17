<?php

use app\commands\MainFunctions;
use common\models\AttributeType;
use common\models\Equipment;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentAttribute */
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
                ['maxlength' => true, 'value' => (new MainFunctions)->GUID()]
            );
    }
    ?>

    <?php
    $types = AttributeType::find()->orderBy('name')->all();
    $items = ArrayHelper::map($types, 'uuid', 'name');
    unset($types);
    echo $form->field($model, 'attributeTypeUuid')->dropDownList($items);
    unset($items);
    ?>

    <?php
    $equipment = Equipment::find()->orderBy(["changedAt" => SORT_DESC])->all();
    $items = ArrayHelper::map($equipment, 'uuid', 'title');
    echo $form->field($model, 'equipmentUuid')->dropDownList($items);
    ?>

    <?php echo $form->field($model, 'value')->textInput(['maxlength' => true]) ?>


    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 300px; margin-bottom: 0;">Дата создания</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'date',
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
