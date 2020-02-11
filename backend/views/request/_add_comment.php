<?php
/* @var $model common\models\Comments
 * @var $entityUuid
 * @var $extParentId
 */

use common\components\MainFunctions;
use common\models\Users;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form2'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить комментарий</h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    echo $form->field($model, 'text')->textInput();
    echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($model, 'entityUuid')->hiddenInput(['value' => $entityUuid])->label(false);
    echo $form->field($model, 'extParentId')->hiddenInput(['value' => $extParentId])->label(false);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>

<script>
    var send = false;
    $(document).on("beforeSubmit", "#form2", function () {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        if (!send) {
            send = true;
            $.ajax({
                url: "../request/save-comment",
                type: "post",
                data: $('form2').serialize(),
                success: function () {
                    $('#modalAddComment').modal('hide');
                },
                error: function () {
                }
            });
        }
    });
</script>
<?php ActiveForm::end(); ?>
