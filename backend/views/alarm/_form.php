<?php

use app\commands\MainFunctions;
use common\models\AlarmStatus;
use common\models\AlarmType;
use common\models\Objects;
use common\models\Users;
use kartik\date\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Alarm */
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
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php

    $alarmType = AlarmType::find()->all();
    $items = ArrayHelper::map($alarmType, 'uuid', 'title');
    echo $form->field($model, 'alarmTypeUuid',
        ['template' => "{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"/alarm-type/create\">
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

    <?php

    $object = Objects::find()->all();
    $items = ArrayHelper::map($object, 'uuid', 'title');
    echo $form->field($model, 'objectUuid')->widget(Select2::class,
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

    <?php echo $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>

    <?php
    $alarmStatus = AlarmStatus::find()->all();
    $items = ArrayHelper::map($alarmStatus, 'uuid', 'title');
    echo $form->field($model, 'alarmStatusUuid')->widget(Select2::class,
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

    <?php echo $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::ORGANISATION_UUID])->label(false); ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px">
        <p style="width: 300px; margin-bottom: 0;">Дата</p>
        <?php echo DatePicker::widget(
            [
                'model' => $model,
                'attribute' => 'date',
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
