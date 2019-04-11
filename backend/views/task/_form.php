<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\commands\MainFunctions;
use common\models\Orders;
use common\models\TaskVerdict;
use common\models\WorkStatus;
use common\models\TaskTemplate;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->textInput(
            ['maxlength' => true,
                'value' => (new MainFunctions)->GUID()]
        );
    }
    ?>

    <?php
    $order = Orders::find()->all();
    $items = ArrayHelper::map($order, 'uuid', 'title');

    if (isset($_GET["order"])) {
        $param = ['options' => [$_GET["order"] => ['Selected' => true]]];
        echo $form->field($model, 'orderUuid')->dropDownList($items, $param);
    } else {
        echo $form->field($model, 'orderUuid')->dropDownList($items);
    }
    ?>

    <?php
    $taskStatus = WorkStatus::find()->all();
    $items = ArrayHelper::map($taskStatus, 'uuid', 'title');
    echo $form->field($model, 'taskStatusUuid')->dropDownList($items);
    ?>

    <?php
    $taskVerdict = TaskVerdict::find()->all();
    $items = ArrayHelper::map($taskVerdict, 'uuid', 'title');
    echo $form->field($model, 'taskVerdictUuid')->dropDownList($items);
    ?>

    <?php
    $taskTemplate = TaskTemplate::find()->all();
    $items = ArrayHelper::map($taskTemplate, 'uuid', 'title');
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);
    ?>

    <?php
    echo $form->field($model, 'comment')
        ->textarea(['rows' => 4, 'style' => 'resize: none;'])
    ?>

    <div class="form-group text-center">

        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'),
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]
        );
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
