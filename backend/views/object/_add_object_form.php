<?php
/* @var $object
 * @var $source
 * @var $houseUuid
 */
use common\components\MainFunctions;
use common\models\ObjectStatus;
use common\models\ObjectType;
use common\models\Users;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../object/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Объект</h4>
</div>
<div class="modal-body">
    <?php
    if ($object['uuid']) {
        echo Html::hiddenInput("objectUuid", $object['uuid']);
        echo $form->field($object, 'uuid')
            ->hiddenInput(['value' => $object['uuid']])
            ->label(false);
    } else {
        echo $form->field($object, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
        echo $form->field($object, 'houseUuid')->hiddenInput(['value' => $houseUuid])->label(false);
        echo $form->field($object, 'objectStatusUuid')->hiddenInput(['value' => ObjectStatus::OBJECT_STATUS_OK])->label(false);
    }
    echo $form->field($object, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($object, 'title')->textInput(['maxlength' => true]);
    echo $form->field($object, 'square')->textInput(['maxlength' => true]);

    echo Html::hiddenInput("type", "object");
    echo Html::hiddenInput("source", $source);

    $types = ObjectType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    echo $form->field($object, 'objectTypeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
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
            url: "../object/save",
            success: function () {
                $('#modalAdd').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
