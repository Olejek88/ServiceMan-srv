<?php
/* @var $documentation */
/* @var $equipmentUuid */
/* @var $equipmentTypeUuid */

use common\components\MainFunctions;
use common\models\DocumentationType;
use common\models\Equipment;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../documentation/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить документацию</h4>
</div>
<div class="modal-body">
    <?php
    if (isset($equipmentUuid) && $equipmentUuid==0) $equipmentUuid=null;
    if (!isset($equipmentUuid)) $equipmentUuid=null;
    if (isset($equipmentTypeUuid) && $equipmentTypeUuid==0) $equipmentTypeUuid=null;
    if (!isset($equipmentTypeUuid)) $equipmentTypeUuid=null;

    echo $form->field($documentation, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    echo $form->field($documentation, 'title')->textInput(['maxlength' => true]);

    $documentationTypes = DocumentationType::find()->all();
    $items = ArrayHelper::map($documentationTypes, 'uuid', 'title');
    echo $form->field($documentation, 'documentationTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите тип  ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);

    echo $form->field($documentation, 'required')->hiddenInput(['value' => 0])->label(false);
    if ($equipmentUuid && $equipmentTypeUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => $equipmentTypeUuid])->label(false);
        echo $form->field($documentation, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false);
    }
    if ($equipmentTypeUuid && !$equipmentUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => $equipmentTypeUuid])->label(false);
    }
    if (!$equipmentTypeUuid && $equipmentUuid) {
        echo $form->field($documentation, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false);
    }
    if (!$equipmentTypeUuid && !$equipmentUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => null])->label(false);
        $equipment = Equipment::find()->all();
        $items = ArrayHelper::map($equipment, 'uuid', 'title');
        echo $form->field($documentation, 'equipmentUuid')->widget(Select2::class,
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
    echo $form->field($documentation, 'path')->widget(
        FileInput::class,
        ['options' => ['accept' => '*'],]
    );
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('backend', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: new FormData( this ),
            processData: false,
            contentType: false
            url: "../documentation/save",
            success: function () {
                $('#modalAddDocumentation').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
