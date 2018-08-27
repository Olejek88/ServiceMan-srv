<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\ActionType;
use common\models\ExternalSystem;
use common\models\Equipment;
use common\models\TaskEquipmentStage;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalTag */
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

        $model->load(Yii::$app->request->post());

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
        }

    ?>

    <?php
    $system = ExternalSystem::find()->all();
    $items = ArrayHelper::map($system,'uuid','title');
    echo $form->field($model, 'systemUuid')->dropDownList($items);
    ?>

    <?= $form->field($model, 'tag')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'equation')->textInput(['value' => 0]) ?>

    <?= $form->field($model, 'target')->textInput(['maxlength' => true]) ?>

    <?php
    $equipment = Equipment::find()->all();
    $items = ArrayHelper::map($equipment,'uuid','title');
    echo $form->field($model, 'equipmentUuid')->dropDownList($items);
    ?>

    <?php
    $action = ActionType::find()->all();
    $items = ArrayHelper::map($action,'uuid','title');
    echo $form->field($model, 'actionTypeUuid')->dropDownList($items);
    ?>

    <?php
    $action = TaskEquipmentStage::find()->all();
    $items = ArrayHelper::map($action,'uuid','taskTemplate.title');
    echo $form->field($model, 'taskEquipmentStageUuid')->dropDownList($items);
    ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
