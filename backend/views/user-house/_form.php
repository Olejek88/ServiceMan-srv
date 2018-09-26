<?php

use common\models\AttributeType;
use common\models\House;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\UserHouse */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="Users-model-form">

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

    <?php
    $users = Users::find()->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');
    echo $form->field($model, 'userUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите пользователя..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <?php
    $houses = House::find()->all();
    $items = ArrayHelper::map($houses, 'uuid', function($model) {
        return $model['street']->title.', '.$model['number'];
    });
    echo $form->field($model, 'houseUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите дом..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

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
