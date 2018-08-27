<?php

use common\models\AttributeType;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\UsersAttribute */
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
        $types = AttributeType::find()->all();
        $items = ArrayHelper::map($types, 'uuid', 'name');
        unset($types);
        echo $form->field($model, 'attributeTypeUuid')->dropDownList($items);
        unset($items);
    ?>

    <?php
        $Users = Users::find()->orderBy(["changedAt" => SORT_DESC])->all();
        $items    = ArrayHelper::map($Users,'uuid','name');
        echo $form->field($model, 'userUuid')->dropDownList($items);
    ?>

    <?php echo $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 200px; margin-bottom: 0;">Дата создания</p>
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
