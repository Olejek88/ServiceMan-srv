<?php

use common\components\MainFunctions;
use common\models\Equipment;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipmentType;
use common\models\TaskType;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\WorkStatus;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить задачу</h4>
</div>
<div class="equipment-status-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'id' => 'form'
        ],
    ]);
    ?>

    <?php
    echo $form->field($model, '_id')->hiddenInput(['value' => $model["_id"]])->label(false);
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }

    if (isset($_GET["equipmentUuid"]))
        echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $_GET["equipmentUuid"]])->label(false);
    else {
        $equipment = Equipment::find()->all();
        $items = ArrayHelper::map($equipment, 'uuid', 'title');
        echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
            [
                'name' => 'kv_type',
                'language' => 'ru',
                'data' => $items,
                'options' => ['placeholder' => 'Выберите элементы ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>

    <?php
    //1 текущий ремонт const TASK_TYPE_CURRENT_REPAIR
    //2 плановый ремонт const TASK_TYPE_PLAN_REPAIR
    //3 текущий осмотр const TASK_TYPE_CURRENT_CHECK
    //4 внеочередной осмотр const TASK_TYPE_NOT_PLANNED_CHECK
    //5 сезонный осмотры const TASK_TYPE_SEASON_CHECK
    //6 плановое обслуживание const TASK_TYPE_PLAN_TO
    //7 внеплановое обслуживание const TASK_TYPE_NOT_PLAN_TO
    //8 устранение аварий const TASK_TYPE_REPAIR
    //9 контроль и поверка const TASK_TYPE_CONTROL
    //10 снятие показаний const TASK_TYPE_MEASURE
    //11 поверка const TASK_TYPE_POVERKA
    //12 монтаж const TASK_TYPE_INSTALL

    if (isset($_GET["equipmentUuid"])) {
        $equipment = Equipment::find()->where(['uuid' => $_GET["equipmentUuid"]])->one();
        $taskTemplate = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where(['equipmentTypeUuid' => $equipment['equipmentTypeUuid']])
            ->andWhere(['or',
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_CONTROL],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_NOT_PLAN_TO],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_MEASURE],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_REPAIR],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_INSTALL],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_CURRENT_REPAIR],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_NOT_PLANNED_CHECK],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_CURRENT_CHECK]])
            ->orderBy('task_template.taskTypeUuid')
            ->all();
        $items = ArrayHelper::map($taskTemplate, 'taskTemplate.uuid', function ($model) {
            return $model['taskTemplate']['taskType']['title'].'::'.$model['taskTemplate']['title'];
        });
        echo $form->field($model, 'taskTemplateUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } else {
        $taskTemplate = TaskTemplateEquipmentType::find()
            ->all();
        $items = ArrayHelper::map($taskTemplate, 'taskTemplate.uuid', 'taskTemplate.title');
        echo $form->field($model, 'taskTemplateUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    ?>
    <?php
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
    echo $form->field($model, 'authorUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
    ?>

    <?php echo $form->field($model, 'workStatusUuid')->hiddenInput(['value' => WorkStatus::NEW])->label(false); ?>
    <?php echo $form->field($model, 'taskVerdictUuid')->hiddenInput(['value' => TaskVerdict::NOT_DEFINED])->label(false); ?>

    <?php
    $users = Users::find()->where(['<>','name','sUser'])->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');
    echo '<label class="control-label">Исполнитель</label>';
    echo Select2::widget(
        [
                'id' => 'userUuid',
            'name' => 'userUuid',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите исполнителя ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    if (isset($_GET["defectUuid"])) {
        echo Html::hiddenInput('defectUuid', $_GET["defectUuid"]);
    }
    ?>

    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
        <p style="width: 0; margin-bottom: 0;">Дата назначения</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'taskDate',
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

    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
        <p style="width: 0; margin-bottom: 0;">Срок</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'deadlineDate',
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

    <?php
        echo $form->field($model, 'comment')
        ->textarea(['rows' => 4, 'style' => 'resize: none;']);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Создать задачу'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>
    <script>
        $(document).on("beforeSubmit", "#dynamic-form", function () {
        }).on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "../task/add-task",
                type: "post",
                data: $('form').serialize(),
                success: function () {
                    $('#modalAddTask').modal('hide');
                },
                error: function () {
                }
            })
        });
    </script>

    <?php ActiveForm::end(); ?>

</div>
