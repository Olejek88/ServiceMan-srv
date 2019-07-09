<?php
/* @var $taskTemplate common\models\TaskTemplate */
/* @var $taskTemplateEquipment common\models\TaskTemplateEquipment */

/* @var $equipment_id */

use common\components\MainFunctions;
use common\models\StageType;
use common\models\TaskTemplate;
use common\models\TaskType;
use common\models\Users;use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить шаблон задачи</h4>
</div>
<div class="modal-body">
    <?php
/*    echo $form->field($taskTemplate, 'stageTemplateUuid')
        ->hiddenInput(['value' => StageType::STAGE_TYPE_VIEW])
        ->label(false);*/
    echo $form->field($taskTemplate, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($taskTemplateEquipment, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);

    if ($taskTemplate['uuid']) {
        echo Html::hiddenInput("taskTemplateUuid", $taskTemplate['uuid']);
        echo $form->field($taskTemplate, 'uuid')
            ->hiddenInput(['value' => $taskTemplate['uuid']])
            ->label(false);
    } else {
        echo $form->field($taskTemplate, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
    }

    if ($taskTemplateEquipment['uuid']) {
        echo $form->field($taskTemplateEquipment, 'uuid')
            ->hiddenInput(['value' => $taskTemplateEquipment['uuid']])
            ->label(false);
    } else {
        echo $form->field($taskTemplateEquipment, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
    }

    $taskTypes = TaskType::find()->all();
    $items = ArrayHelper::map($taskTypes, 'uuid', 'title');
    echo $form->field($taskTemplate, 'taskTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите тип ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);

    echo $form->field($taskTemplate, 'title')->textInput();
    echo $form->field($taskTemplate, 'description')->textArea(['rows' => '6']);
    echo $form->field($taskTemplate, 'normative')->textInput();

    echo '<label class="control-label">Период</label>';
    echo $form->field($taskTemplateEquipment, 'period')->textInput();

    echo Html::hiddenInput("equipment_id", $equipment_id);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "new",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                console.log("success?!");
                $('#modalAddOperation').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function (e) {
        e.preventDefault();
    });

</script>
<?php ActiveForm::end(); ?>
