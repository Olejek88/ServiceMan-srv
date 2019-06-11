<?php

use common\models\Equipment;
use common\models\TaskTemplate;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\WorkStatus;
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
                'options' => ['placeholder' => 'Выберите оборудование ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false); ?>

    <?php
    $taskTemplate = TaskTemplate::find()->all();
    $items = ArrayHelper::map($taskTemplate, 'uuid', 'title');
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);

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
