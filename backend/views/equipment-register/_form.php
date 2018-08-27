<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\EquipmentRegisterType;
use yii\helpers\ArrayHelper;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentRegister */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
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
        $registerTypes = EquipmentRegisterType::find()->orderBy("title")->all();
        $items = ArrayHelper::map($registerTypes,'uuid','title');
        echo $form->field($model, 'registerTypeUuid')->dropDownList($items);

        $equipment = Equipment::find()->orderBy("title")->all();
        $items = ArrayHelper::map($equipment,'uuid','title');
        echo $form->field($model, 'equipmentUuid')->dropDownList($items);

        $user  = Users::find()->all();
        $items = ArrayHelper::map($user,'uuid','name');
        echo $form->field($model, 'userUuid')->dropDownList($items);
    ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
    <p style="width: 0; margin-bottom: 0;">Дата</p>
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

    <?= $form->field($model, 'fromParameterUuid')->textInput(['value' => 0]) ?>

    <?= $form->field($model, 'toParameterUuid')->textInput(['value' => 0]) ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
