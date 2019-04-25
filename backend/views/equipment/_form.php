<?php

use common\components\MainFunctions;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Objects;
use common\models\Users;
use kartik\date\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-documentation',
            'options' => [
                'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                'enctype' => 'multipart/form-data'
            ],
        ]
    ); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    $equipmentType = EquipmentType::find()->all();
    $items = ArrayHelper::map($equipmentType, 'uuid', 'title');
    echo $form->field($model, 'equipmentTypeUuid',
        ['template' => MainFunctions::getAddButton("/equipment-type/create")])->widget(Select2::class,
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

    <?php

    $equipmentStatus = EquipmentStatus::find()->all();
    $items = ArrayHelper::map($equipmentStatus, 'uuid', 'title');
    echo $form->field($model, 'equipmentStatusUuid',
        ['template' => MainFunctions::getAddButton("/equipment-status/create")])->widget(Select2::class,
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

    <?php echo $form->field($model, 'serial')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'tag')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::ORGANISATION_UUID])->label(false); ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px">
        <p style="width: 300px; margin-bottom: 0;">Дата поверки</p>
        <?php echo DatePicker::widget(
            [
                'model' => $model,
                'attribute' => 'testDate',
                'language' => 'ru',
                'size' => 'ms',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]
        );
        ?>
    </div>

    <?php
    $object = Objects::find()->all();
    $items = ArrayHelper::map($object, 'uuid', function ($model) {
        return $model['house']['street']->title . ', ' . $model['house']->number . ', ' . $model['title'];
    });
    echo $form->field($model, 'objectUuid')->widget(Select2::class,
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

    <div class="form-group text-center">

        <?php
        if ($model->isNewRecord) {
            $buttonText = Yii::t('app', 'Создать');
            $buttonClass = 'btn btn-success';
        } else {
            $buttonText = Yii::t('app', 'Обновить');
            $buttonClass = 'btn btn-primary';
        }

        echo Html::submitButton($buttonText, ['class' => $buttonClass]);
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
