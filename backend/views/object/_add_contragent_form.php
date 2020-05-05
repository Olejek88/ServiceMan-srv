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
    'options' => [
        'id' => 'add-contragent-form',
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
    if ($(document).data('add-contragent-form') === true) {
    } else {
        $(document).data('add-contragent-form', true);
        $(document)
            .on("beforeSubmit", "#add-contragent-form", function (e) {
                e.preventDefault();
            })
            .on('submit', "#add-contragent-form", function (e) {
                e.preventDefault();
                var form = $(this);
                if (form.data('submited') === true) {
                } else {
                    form.data('submited', true);
                    $.ajax({
                        url: "../object/save",
                        type: "post",
                        data: form.serialize(),
                        success: function () {
                            $('#modalAdd').modal('hide');
                        },
                        error: function (error) {
                            // когда на ajax запрос отвечают редиректом, генерируется ошибка
                            if (error.status !== 302) {
                                // если это не редирект, включаем возможность повторной отправки формы
                                form.data('submited', false);
                            }

                            if (error.status === 302) {
                                // если редирект, считаем что всё в порядке
                                $('#modalAdd').modal('hide');
                            }
                        }
                    });
                }
            });
    }
</script>
<?php ActiveForm::end(); ?>
