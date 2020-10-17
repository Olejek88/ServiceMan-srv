<?php
/*  @var $street
 *  @var $source
 */

use common\components\MainFunctions;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Улица</h4>
</div>
<div class="modal-body">
    <?php
    if ($street['uuid']) {
        echo Html::hiddenInput("streetUuid", $street['uuid']);
        echo $form->field($street, 'uuid')
            ->hiddenInput(['value' => $street['uuid']])
            ->label(false);
    } else {
        echo $form->field($street, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
    }
    //    echo $form->field($street, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo Html::hiddenInput("type", "street");
    echo Html::hiddenInput("source", $source);
    echo $form->field($street, 'title')->textInput(['maxlength' => true]);
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
        var me = $('button.btn.btn-success', e.target);
        me.prop('disabled', true).removeClass('enabled').addClass('disabled');
        var form = $('#form');
        $.ajax({
            type: "post",
            data: form.serialize(),
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
