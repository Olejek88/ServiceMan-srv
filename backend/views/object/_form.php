<?php

use app\commands\MainFunctions;
use common\models\House;
use common\models\ObjectStatus;
use common\models\ObjectType;
use common\models\Users;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Objects */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>

    <?php
    $houses = House::find()->all();
    $items = ArrayHelper::map($houses, 'uuid', function ($model) {
        return $model['street']->title . ', ' . $model['number'];
    });
    echo $form->field($model, 'houseUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите объект..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?php echo $form->field($model, 'objectStatusUuid')
        ->hiddenInput(['value' => ObjectStatus::OBJECT_STATUS_OK])->label(false); ?>

    <?php
    $types = ObjectType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    echo $form->field($model, 'objectTypeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?php echo $form->field($model, 'square')->textInput(['maxlength' => true]) ?>

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
