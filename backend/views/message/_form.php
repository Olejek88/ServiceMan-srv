<?php

use app\commands\MainFunctions;
use common\models\User;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-type-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
            'enctype' => 'multipart/form-data'
        ],
    ]); ?>

    <?php

        $model->load(Yii::$app->request->post());

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
        }
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>

    <?php
    $user = Users::find()
        ->joinWith('user')
        ->andWhere(['user.status' => User::STATUS_ACTIVE])
        ->all();
    $items = ArrayHelper::map($user,'uuid','name');
    echo $form->field($model, 'fromUserUuid')->dropDownList($items);
    ?>

    <?php
    $user = Users::find()
        ->joinWith('user')
        ->andWhere(['user.status' => User::STATUS_ACTIVE])
        ->all();
    $items = ArrayHelper::map($user,'uuid','name');
    echo $form->field($model, 'toUserUuid')->dropDownList($items);
    ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 0; margin-bottom: 0;">Дата</p>
        <?php echo $form->field($model, 'date')->widget(DateTimePicker::class,
            [
                'language' => 'ru',
                'size' => 'ms',
                'clientOptions' => [
                    'autoclose' => true,
                    'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                    'todayBtn' => true,
                ]
            ]
        );
        ?>
    </div>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
