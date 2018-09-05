<?php

use app\commands\MainFunctions;
use common\models\FlatStatus;
use common\models\House;
use common\models\TaskTemplate;
use common\models\TaskVerdict;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Flat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->textInput(
            ['maxlength' => true,
                'value' => (new MainFunctions)->GUID()]
        );
    }
    ?>

    <?php echo $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?php
    $houses = House::find()->all();
    $items = ArrayHelper::map($houses, 'uuid', function($model) {
        return $model['street']->title.', '.$model['number'];
    });
    echo $form->field($model, 'houseUuid')->widget(Select2::classname(),
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите квартиру..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    $statuses = FlatStatus::find()->all();
    $items = ArrayHelper::map($statuses, 'uuid', 'title');
    echo $form->field($model, 'flatStatusUuid')->widget(Select2::classname(),
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите статус..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <div class="form-group text-center">
        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'),
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]
        );
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
