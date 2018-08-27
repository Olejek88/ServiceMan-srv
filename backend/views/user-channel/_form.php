<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\commands\MainFunctions;
use common\models\MessageChannel;
use common\models\MessageType;
use common\models\Users;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php

    $model->load(Yii::$app->request->post());

    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
    }

    ?>

    <?php
    $channels = MessageChannel::find()->all();
    $items = ArrayHelper::map($channels,'uuid','title');
    echo $form->field($model, 'messageChannelUuid')->dropDownList($items);
    ?>

    <?php
    $types = MessageType::find()->all();
    $items = ArrayHelper::map($types,'uuid','title');
    echo $form->field($model, 'messageTypeUuid')->dropDownList($items);
    ?>

    <?php
    $users = Users::find()->all();
    $items = ArrayHelper::map($users,'uuid','name');
    echo $form->field($model, 'userUuid')->dropDownList($items);
    ?>

    <?= $form->field($model, 'channelId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
