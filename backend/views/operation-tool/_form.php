<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;
use common\models\OperationTemplate;
use common\models\Tool;

/* @var $this yii\web\View */
/* @var $model common\models\OperationTool */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operation-tool-form">

    <?php $form = ActiveForm::begin(); ?>

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

    $templates = OperationTemplate::find()->all();
    $items = ArrayHelper::map($templates, 'uuid', 'title');
    unset($templates);
    echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);
    unset($items);
    
    ?>

    <?php

    $tools = Tool::find()->all();
    $items = ArrayHelper::map($tools, 'uuid', 'title');
    unset($tools);
    echo $form->field($model, 'toolUuid')->dropDownList($items);
    unset($items);

    ?>

    <?php
    $value = isset($model->quantity) ? $model->quantity : 1;
    echo $form->field($model, 'quantity')
        ->textInput(['maxlength' => true, 'value' => $value]);
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
