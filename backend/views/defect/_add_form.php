<?php
/* @var $model */

/* @var $equipmentUuid */


use common\components\MainFunctions;
use common\models\AttributeType;
use common\models\DefectType;
use common\models\Equipment;
use common\models\Users;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../defect/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить дефект</h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($model, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);

    echo $form->field($model, 'defectStatus')->hiddenInput(['value' => 0])->label(false);
    echo $form->field($model, 'title')->textarea(['rows' => 4, 'style' => 'resize: none;']);
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

    $user = Users::find()->all();
    $items = ArrayHelper::map($user, 'uuid', 'name');
    echo $form->field($model, 'userUuid')->dropDownList($items);

    echo $form->field($model, 'date')
        ->hiddenInput(['value' => date("Ymd")])
        ->label(false);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('backend', 'Добавить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: new FormData(this),
            processData: false,
            contentType: false
            url: "../defect/save",
            success: function () {
                $('#modal_new').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
