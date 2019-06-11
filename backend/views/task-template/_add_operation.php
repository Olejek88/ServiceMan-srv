<?php
/* @var $operationTemplate common\models\OperationTemplate */
/* @var $taskTemplateUuid */
/* @var $taskTemplateEquipment */

use common\components\MainFunctions;
use common\models\OperationType;
use common\models\Users;
use kartik\select2\Select2;
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
    <h4 class="modal-title">Шаблон операции</h4>
</div>
<div class="modal-body">
    <?php
    if ($operationTemplate['uuid']) {
        echo Html::hiddenInput("operationTemplateUuid", $operationTemplate['uuid']);
        echo $form->field($operationTemplate, 'uuid')
            ->hiddenInput(['value' => $operationTemplate['uuid']])
            ->label(false);
    } else {
        echo $form->field($operationTemplate, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
        echo Html::hiddenInput("taskTemplateUuid", $taskTemplateUuid);
    }
    echo $form->field($operationTemplate, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false);
    echo $form->field($operationTemplate, 'title')->textInput();
    echo $form->field($operationTemplate, 'description')->textArea(['rows' => '8']);
//    echo Html::hiddenInput("equipment_uuid", $equipment_uuid);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "operation",
            type: "post",
            data: $('form').serialize(),
            success: function () {
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
