<?php

use common\models\Task;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

use common\models\WorkStatus;
use common\models\OperationTemplate;

/* @var $this yii\web\View */
/* @var $model common\models\Operation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operation-form">

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
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }

    ?>

    <?php

    $tasks = Task::find()->all();
    $items = ArrayHelper::map($tasks, 'uuid', 'comment');
    $params = [
        'prompt' => 'Выберите задачу..',
    ];

    echo $form->field($model, 'taskUuid')->dropDownList($items, $params);

    ?>

    <?php

    $operationStatus = WorkStatus::find()->all();
    $items = ArrayHelper::map($operationStatus, 'uuid', 'title');

    echo $form->field($model, 'workStatusUuid')->dropDownList($items);

    ?>

    <?php

    $operationTemplate = OperationTemplate::find()->all();
    $items = ArrayHelper::map($operationTemplate, 'uuid', 'title');

    echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);

    ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
