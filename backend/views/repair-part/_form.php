<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\commands\MainFunctions;
use common\models\RepairPartType;

/* @var $this yii\web\View */
/* @var $model common\models\RepairPart */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $uuidField = $form->field($model, 'uuid');

    if (!$model->isNewRecord) {
        $uuidFieldValue = $uuidField->textInput(
            ['maxlength' => true, 'readonly' => true]
        );
    } else {
        $uuidFieldValue = $uuidField->textInput(
            ['maxlength' => true, 'value' => (new MainFunctions)->GUID()]
        );
    }

    echo $uuidFieldValue;
    ?>

    <?php
    $types = RepairPartType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    echo $form->field($model, 'repairPartTypeUuid')->dropDownList($items);
    unset($items);
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

        echo Html::submitButton($buttonText, ['class' => $buttonClass]);
        ?>

    </div>

    <h6 class='text-center'>
        * Если вы не нашли <b><a href="/repair-part-type">тип ЗИП</a></b>, который вам нужен, создайте его
    </h6>

    <?php ActiveForm::end(); ?>

</div>
