<?php
/* @var $contragent
 * @var $source
 * @var $uuid
 * @var $address
 * @var $objectUuid
 */

use common\components\MainFunctions;
use common\models\ContragentType;
use common\models\Users;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '/object/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Контрагент</h4>
</div>
<div class="modal-body">
    <?php
    if ($contragent['uuid']) {
        echo Html::hiddenInput("contragentUuid", $contragent['uuid']);
        echo $form->field($contragent, 'uuid')
            ->hiddenInput(['value' => $contragent['uuid']])
            ->label(false);
    } else {
        echo $form->field($contragent, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
        echo Html::hiddenInput("objectUuid", $objectUuid);
    }
    echo $form->field($contragent, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($contragent, 'title')->textInput(['maxlength' => true]);
    echo $form->field($contragent, 'address')->textInput(['maxlength' => true, 'value' => $address]);
    echo $form->field($contragent, 'phone')->textInput(['maxlength' => true]);
    echo $form->field($contragent, 'inn')->textInput(['maxlength' => true]);
    echo $form->field($contragent, 'director')->textInput(['maxlength' => true]);
    echo $form->field($contragent, 'email')->textInput(['maxlength' => true]);

    echo Html::hiddenInput("type", "contragent");
    echo Html::hiddenInput("source", $source);

    $contragentType = ContragentType::find()->all();
    $items = ArrayHelper::map($contragentType, 'uuid', 'title');
    echo $form->field($contragent, 'contragentTypeUuid')->widget(Select2::class,
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
