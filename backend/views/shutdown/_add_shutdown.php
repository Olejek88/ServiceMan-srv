<?php
/* @var $model common\models\Shutdown */

use common\components\MainFunctions;
use common\models\RequestStatus;
use common\models\Users;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options'                => [
            'id'      => 'form'
        ]]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить аварийное отключение</h4>
    </div>
    <div class="modal-body">
        <?php
            echo $form->field($model, 'uuid')->hiddenInput(['value' => MainFunctions::GUID()])->label(false);
            if (isset($_GET["equipmentUuid"]))
                echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $_GET["equipmentUuid"]])->label(false);
            if (isset($_GET["objectUuid"]))
                echo $form->field($model, 'objectUuid')->hiddenInput(['value' => $_GET["objectUuid"]])->label(false);
            echo $form->field($model, 'comment')->textArea();
        ?>
    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "../request/new",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modal_request').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function(e){
        e.preventDefault();
    });
</script>
<?php ActiveForm::end(); ?>
