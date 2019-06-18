<?php

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\Objects;
use common\models\RequestType;
use common\models\Task;
use common\models\Users;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Equipment;
use common\models\requestStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-request-form" style="min-height: 900px">

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
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php
    echo $form->field($model, 'type')->widget(Select2::class,
        [
            'data' => [0 => "Бесплатная заявка", 1 => "Платная заявка"],
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
    $users = Contragent::find()->orderBy('title DESC')->all();
    $items = ArrayHelper::map($users, 'uuid', 'title');
    echo $form->field($model, 'userUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Заявитель'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

<!--    --><?php
/*    $contragents = Contragent::find()->all();
    $items = ArrayHelper::map($contragents, 'uuid', 'title');
    echo $form->field($model, 'contragentUuid',
        ['template' => MainFunctions::getAddButton("/contragent/create")])->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите исполнителя..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    */?>

    <?php
    $type = RequestType::find()->all();
    $items = ArrayHelper::map($type, 'uuid', 'title');
    echo $form->field($model, 'requestTypeUuid',
        ['template' => MainFunctions::getAddButton("/request-type/create")])->widget(Select2::class,
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
    $equipments = Equipment::find()->all();
    $items = ArrayHelper::map($equipments, 'uuid', 'title');
    echo $form->field($model, 'equipmentUuid',
        ['template' => MainFunctions::getAddButton("/equipment/create")])->widget(Select2::class,
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
    $objects  = Objects::find()->all();
    $items = ArrayHelper::map($objects,'uuid','title');
    echo $form->field($model, 'objectUuid',
        ['template' => MainFunctions::getAddButton("/object/create")])->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите объект..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    $tasks = Task::find()->all();
    $items = ArrayHelper::map($tasks, 'uuid', 'taskTemplate.title');
    echo $form->field($model, 'taskUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Задача'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
    echo $form->field($model, 'authorUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
    echo $form->field($model, 'requestStatusUuid')->hiddenInput(['value' => RequestStatus::NEW_REQUEST])->label(false);
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false); ?>
    <?= $form->field($model, 'userCheck')->textInput() ?>
    <?= $form->field($model, 'comment')->textInput() ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
