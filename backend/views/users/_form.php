<?php

use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

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

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'pin')->textInput(['maxlength' => true]) ?>

    <?php
    $users = User::find()->all();
    $items = ArrayHelper::map($users, 'id', 'username');
    echo $form->field($model, 'user_id')->dropDownList($items);
    ?>

    <?php echo $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

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
