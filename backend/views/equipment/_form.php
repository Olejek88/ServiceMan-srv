<?php

use app\commands\MainFunctions;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Object;
use common\models\House;
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
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')
            ->textInput(
                ['maxlength' => true, 'readonly' => true, 'value' => (new MainFunctions)->GUID()]
            );
    }

    ?>

    <?php

    $equipmentType = EquipmentType::find()->all();
    $items = ArrayHelper::map($equipmentType, 'uuid', 'title');
    echo $form->field($model, 'equipmentTypeUuid',
        ['template' => "{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"/equipment-type/create\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}"])->widget(Select2::classname(),
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
        ['template' => "{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"/equipment-status/create\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}"])->widget(Select2::class,
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
    $house = House::find()->all();
    $items = ArrayHelper::map($house, 'uuid', function ($model) {
        return $model['street']->title . ', ' . $model['number'];
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

    <?php
    $flat = Object::find()->all();
    $items = ArrayHelper::map($flat, 'uuid', function ($model) {
        return $model['house']['street']->title . ', ' . $model['house']->number . ', ' . $model['number'];
    });
    echo $form->field($model, 'flatUuid')->widget(Select2::class,
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
