<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\EquipmentType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentType */
/* @var $form yii\widgets\ActiveForm */
/* @var $parentModel yii\base\DynamicModel */
?>

<div class="equipment-type-form">

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

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

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
