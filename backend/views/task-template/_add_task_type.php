<?php
/* @var $taskTemplate common\models\TaskTemplate */
/* @var $equipmentTypeUuid */
/* @var $types */

use common\components\MainFunctions;
use common\models\TaskType;
use common\models\Users;
use kartik\widgets\Select2;
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
    echo $form->field($taskTemplate, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);

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

    echo Html::hiddenInput("equipmentTypeUuid", $equipmentTypeUuid);

    if ($types) {
        echo $form->field($taskTemplate, 'taskTypeUuid')->hiddenInput(['value' => $types])->label(false);
    } else {
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
    }
    echo $form->field($taskTemplate, 'title')->textInput();
    echo $form->field($taskTemplate, 'description')->textArea(['rows' => '6']);
    echo $form->field($taskTemplate, 'normative')->textInput();

    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "new-template",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                console.log("success?!");
                $('#modalAddTask').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function (e) {
        e.preventDefault();
    });

</script>
<?php ActiveForm::end(); ?>
