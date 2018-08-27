<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

use kalyabin\maplocation\SelectMapLocationWidget;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\Objects;
use common\models\ObjectType;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Objects */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="objects-form box-padding">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-documentation',
            'options' => [
                'class' => 'form-horizontal col-lg-11 col-sm-11 col-xs-11',
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

    $objects = Objects::find()->all();
    $items = array(
        '00000000-0000-0000-0000-000000000000' => 'Якорь верхнего уровня',
        '00000000-0000-0000-0000-000000000001' => 'Якорь среднего уровня',
        '00000000-0000-0000-0000-000000000002' => 'Якорь нижнего уровня',
    );
    $items += ArrayHelper::map($objects, 'uuid', 'title');
    $countItems = count($items);
    $isItems = $countItems != 0;

    if (!$isItems) {
        echo $form->field($model, 'parentUuid')->dropDownList(
            [
                '00000000-0000-0000-0000-000000000000' => 'Якорь верхнего уровня',
                '00000000-0000-0000-0000-000000000001' => 'Якорь среднего уровня',
                '00000000-0000-0000-0000-000000000002' => 'Якорь нижнего уровня',
            ]
        );
    } else {
        echo $form->field($model, 'parentUuid')->widget(Select2::classname(),
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
    }

    ?>

    <?php

    $objectTypeUuid = ObjectType::find()->all();
    $items = ArrayHelper::map($objectTypeUuid, 'uuid', 'title');
    $countItems = count($items);
    $isItems = $countItems != 0;

    if ($isItems) {
        echo $form->field($model, 'objectTypeUuid',
            ['template'=>'{label}<div class="input-group">{input}<span class="input-group-btn" style="padding-left: 5px">
        <a href="/object-type/create">
        <button class="btn btn-success" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        </button></a></span></div>{hint}{error}'])->widget(Select2::classname(),
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
    } else {
        echo $form->field($model, 'objectTypeUuid')->dropDownList(
            [
                '00000000-0000-0000-0000-000000000004' => 'Данных нет'
            ]
        );
    }

    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    $addressModel = new \yii\base\DynamicModel(['address', 'latitude', 'longitude']);
    $addressModel->addRule(['address'], 'string', ['max' => 128]);
    $addressModel->addRule(['latitude'], 'number');
    $addressModel->addRule(['longitude'], 'string');

    echo $form->field($addressModel, 'address')
        ->label('Адрес')
        ->widget(SelectMapLocationWidget::className(), [
        'attributeLatitude' => 'latitude',
        'attributeLongitude' => 'longitude',
        'draggable' => true,
        'googleMapApiKey' => 'AIzaSyA7cevgV0L3QYoonykvmOgJHgMKyZL8k-I'
    ]);
    ?>

    <?php echo $form->field($model, 'description')
    ->textarea(['rows' => 4, 'style' => 'resize: none;']) ?>

    <?php echo $form->field($model, 'photo')
    ->widget(FileInput::classname(), ['options' => ['accept' => '*'],]); ?>

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
