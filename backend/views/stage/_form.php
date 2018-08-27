<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\Task;
use common\models\StageStatus;
use common\models\StageVerdict;
use common\models\StageTemplate;

/* @var $this yii\web\View */
/* @var $model common\models\Stage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-stage-form">

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

    <?= $form->field($model, 'comment')->textarea(['rows' => 4, 'style' => 'resize: none;']) ?>

    <?php

    $task = Task::find()->all();
    $items = ArrayHelper::map($task, 'uuid', 'comment');

    //echo $form->field($model, 'taskUuid')->dropDownList($items);

    ?>

    <?php
    $equipments = Equipment::find()->all();
    $items = ArrayHelper::map($equipments, 'uuid', 'title', 'inventoryNumber');
    $params = [
        'prompt' => 'Выберите оборудование..',
    ];

    echo Select2::widget([
        'name' => 'kv_lang_select1',
        'language' => 'ru',
        'data' => $items,
        'options' => ['placeholder' => 'Выберите состояние ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    echo $form->field($model, 'equipmentUuid')->dropDownList($items, $params);
    ?>

    <?php

    $stageStatus = StageStatus::find()->all();
    $items = ArrayHelper::map($stageStatus, 'uuid', 'title');

    echo $form->field($model, 'stageStatusUuid')->dropDownList($items);

    ?>

    <?php

    $stageVerdicts = StageVerdict::find()->all();
    $items = ArrayHelper::map($stageVerdicts, 'uuid', 'title');

    echo $form->field($model, 'stageVerdictUuid')->dropDownList($items);

    ?>

    <?php

    $stageTemplates = StageTemplate::find()->all();
    $items = ArrayHelper::map($stageTemplates, 'uuid', 'title');

    echo $form->field($model, 'stageTemplateUuid')->dropDownList($items);

    ?>

    <?= $form->field($model, 'endDate')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'flowOrder')->textInput() ?>


    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
