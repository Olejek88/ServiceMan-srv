<?php

use common\models\Users;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\OperationTemplate;
use yii\helpers\ArrayHelper;
use common\models\TaskTemplate;
use app\commands\MainFunctions;

/* @var $this yii\web\View */
/* @var $model common\models\TaskOperation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-operation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php
    $tasks = TaskTemplate::find()->all();
    $items = ArrayHelper::map($tasks, 'uuid', 'title');
    unset($tasks);
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);
    unset($items);

    ?>

    <?php
    $templates = OperationTemplate::find()->all();
    $items = ArrayHelper::map($templates, 'uuid', 'title');
    unset($templates);
    echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);
    unset($items);
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
