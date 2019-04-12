<?php

use common\models\Object;
use common\models\Users;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\requestStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-request-form">

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
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'value' => MainFunctions::GUID()]);
    }

    ?>

    <?php
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['userId' => $accountUser['id']]);
    echo $form->field($model, 'userUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);

    echo $form->field($model, 'requestStatusUuid')->hiddenInput(['value' => RequestStatus::NEW_REQUEST])->label(false);
/*
    $requestStatus = RequestStatus::find()->all();
    $items       = ArrayHelper::map($requestStatus,'uuid','title');
    echo $form->field($model, 'requestStatusUuid')->dropDownList($items);*/
    ?>

    <?= $form->field($model, 'comment')->textInput() ?>

    <?php
    echo 'Оборудование';
    $equipments = Equipment::find()->all();
    $items = ArrayHelper::map($equipments, 'uuid', 'title', 'inventoryNumber');
    echo Select2::widget([
        'name' => 'kv_lang_select1',
        'language' => 'ru',
        'data' => $items,
        'options' => ['placeholder' => 'Выберите оборудование ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?php
    $objects  = Object::find()->all();
    $items = ArrayHelper::map($objects,'uuid','title');
    echo $form->field($model, 'objectUuid')->dropDownList($items);
    ?>

    <?= $form->field($model, 'closeDate')->textInput(['readonly' => true]) ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
