<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\commands\MainFunctions;
use common\models\TaskType;

use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\TaskTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-template-form">

    <?php
    $form = ActiveForm::begin(
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

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'description')->textarea(
        ['rows' => 4, 'style' => 'resize: none;']
    );
    ?>

    <?php
    echo $form->field($model, 'image')->widget(
        FileInput::classname(), ['options' => ['accept' => '*']]
    );
    ?>

    <?php echo $form->field($model, 'normative')->textInput() ?>

    <?php
    $types = TaskType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    echo $form->field($model, 'taskTypeUuid')->dropDownList($items);
    unset($items);
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
