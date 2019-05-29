<?php
/* @var $equipment common\models\Equipment */

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
    <h4 class="modal-title">Выбрать шаблон задачи</h4>
</div>
<div class="modal-body">
    <?php

    $taskTemplates = TaskTemplate::find()->all();
    $items = ArrayHelper::map($taskTemplates, 'uuid', 'title');
    echo Select2::widget(
        [
            'id' => 'taskTemplateUuid',
            'name' => 'taskTemplateUuid',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите шаблон ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo '<label class="control-label">Период</label><br/>';
    echo Html::textInput("period");

    echo Html::hiddenInput("equipment_uuid", $equipment['uuid']);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "choose",
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
