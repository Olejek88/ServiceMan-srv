<?php

use common\models\DefectType;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\datetimepicker\DateTimePicker;
use app\commands\MainFunctions;
use common\models\Users;
use common\models\Equipment;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false); ?>

    <?php
    echo $form->field($model, 'defectStatus')->hiddenInput(['value' => 0])->label(false);
    $defect_types = DefectType::find()->all();
    $items = ArrayHelper::map($defect_types, 'uuid', 'title');
    echo $form->field($model, 'defectTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_type',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите тип ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($model, 'title')->textarea(['rows' => 4, 'style' => 'resize: none;']);
    $equipment = Equipment::find()->all();
    $items = ArrayHelper::map($equipment, 'uuid', 'title');
    echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
        [
            'name' => 'kv_type',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите элемент ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    $user = Users::find()->all();
    $items = ArrayHelper::map($user, 'uuid', 'name');
    echo $form->field($model, 'userUuid')->dropDownList($items);

    ?>

    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
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

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
