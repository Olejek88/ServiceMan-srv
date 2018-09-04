<?php

use common\models\Users;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;
use common\models\Equipment;
use dosamigos\datetimepicker\DateTimePicker;


/* @var $this yii\web\View */
/* @var $model common\models\Measure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="measured-value-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-11',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
        }
    ?>

    <?php
    $equipments = Equipment::find()->all();
    $items = ArrayHelper::map($equipments, 'uuid', function($model) {
        return $model['equipmentType']->title.' ('.$model['flat']['house']['street']->title.', '.
            $model['flat']['house']->number.', '.
            $model['flat']->number.')';
    });
    echo $form->field($model, 'equipmentUuid')->widget(Select2::classname(),
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите оборудование..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?php
    $users = Users::find()->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');
    echo $form->field($model, 'userUuid')->widget(Select2::classname(),
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

    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 200px; margin-bottom: 0;">Дата измерения</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'date',
            'language' => 'ru',
            'size' => 'ms',
            'clientOptions' => [
                'autoclose' => true,
                'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                'todayBtn' => true
            ]
        ]);
        ?>
    </div>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
