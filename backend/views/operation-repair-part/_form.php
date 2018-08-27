<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\OperationTemplate;
use yii\helpers\ArrayHelper;
use common\models\RepairPart;

/* @var $this yii\web\View */
/* @var $model common\models\OperationRepairPart */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operation-repair-part-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-operation-repair-part',
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
                ['maxlength' => true, 'value' => (new MainFunctions)->GUID()]
            );
    }
    ?>

    <?php
    $types = OperationTemplate::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);
    unset($items);
    ?>

    <?php
    $types = RepairPart::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    echo $form->field($model, 'repairPartUuid')->dropDownList($items);
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
