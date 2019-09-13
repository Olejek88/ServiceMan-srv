<?php

use app\commands\MainFunctions;
use common\models\TaskTemplate;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RequestType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-status-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?php
    $taskTemplate = TaskTemplate::find()->all();
    $items = ArrayHelper::map($taskTemplate, 'uuid', 'title');
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);
    ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
