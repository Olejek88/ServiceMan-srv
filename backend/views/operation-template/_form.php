<?php

use common\models\Users;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;
use common\models\OperationType;


use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\OperationTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operation-template-form">

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
    // таким макаром можно отобразить список всех ошибок в форме
    //echo $form->errorSummary($model, ['header' => 'Исправьте ошибки']);
    ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'description')->textarea(
        ['rows' => 4, 'style' => 'resize: none;']
    );
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::ORGANISATION_UUID])->label(false); ?>

    <div class="form-group text-center">
        <?php
        if ($model->isNewRecord) {
            $msg = Yii::t('app', 'Создать');
            $class = 'btn btn-success';
        } else {
            $msg = Yii::t('app', 'Обновить');
            $class = 'btn btn-primary';
        }

        echo Html::submitButton($msg, ['class' => $class]);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
