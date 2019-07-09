<?php
/* @var $model common\models\EquipmentRegister */
/* @var $equipmentUuid */

use common\components\MainFunctions;
use common\models\Equipment;
use common\models\EquipmentRegisterType;
use common\models\Users;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'id' => 'form'
        ]]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить запись в журнал</h4>
    </div>
    <div class="modal-body">
        <?php
            echo $form->field($model, 'uuid')->hiddenInput(['value' => MainFunctions::GUID()])->label(false);
            $accountUser = Yii::$app->user->identity;
            $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
            echo $form->field($model, 'userUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
        echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);

            $registerTypes = EquipmentRegisterType::find()->orderBy("title")->all();
            $items = ArrayHelper::map($registerTypes,'uuid','title');
            echo $form->field($model, 'registerTypeUuid')->dropDownList($items);

            echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false);
            echo $form->field($model, 'description')->textInput(['value' => 'без описания']);
        ?>
    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function(e){
        console.log("1");
        e.preventDefault();
        $.ajax({
            url: "../equipment-register/new",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modalChange').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>