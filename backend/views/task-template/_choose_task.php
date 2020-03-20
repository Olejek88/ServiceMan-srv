<?php

/** @var $taskTemplates */

/** @var $equipmentUuid */

use kartik\date\DatePicker;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'choose_form'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Выбрать шаблон задачи</h4>
</div>
<br class="modal-body">
<?php
echo Select2::widget([
    'id' => 'taskTemplateUuid',
    'name' => 'taskTemplateUuid',
    'language' => 'ru',
    'data' => $taskTemplates,
    'options' => ['placeholder' => 'Выберите шаблон ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
echo '<label class="control-label">Период (дн.)</label><br/>';
echo Html::textInput("period");
echo Html::hiddenInput("equipment_uuid", $equipmentUuid);
?>

<br/><br/>
<label>Дата отсчета</label>
<div class="pole-mg" style="margin: 2px 2px 2px 5px;">
    <?= DatePicker::widget([
        'id' => 'last_date',
        'name' => 'last_date',
        'removeButton' => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ])
    ?>
</div>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#choose_form", function () {
        $.ajax({
            url: "choose",
            type: "post",
            data: $('#choose_form').serialize(),
            success: function () {
                console.log("success?!");
                $('#modalAddOperation').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', '#choose_form', function (e) {
        e.preventDefault();
    });

</script>
<?php ActiveForm::end(); ?>
