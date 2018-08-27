<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;

/* @var $this yii\web\View */
/* @var $model common\models\AttributeType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="critical-type-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-11',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php

        $model->load(Yii::$app->request->post());

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID(),'readOnly'=> true]);
        }

    ?>
    <?php
        $items = [
            '1' => 'Файл',
            '2' => 'Числовое значение',
            '3' => 'Строка' ];
        $params = ['prompt' => 'Выберите тип аттрибута...'];
        echo $form->field($model, 'type')->dropDownList($items,$params);
    ?>
    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'units')->textInput() ?>

    <?= $form->field($model, 'refresh')
        ->checkbox([
            'label' => 'Обновляемый',
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ]
        ]);
    ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
