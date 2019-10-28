<?php
/* @var $model common\models\Request
 * @var $receiptUuid string
 * @var $source string
 * @var $path string
 */

use common\components\MainFunctions;
use common\models\ContragentType;
use common\models\Users;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form'
    ]]);
?>
<div class="modal-header">
    <h4 class="modal-title">Добавить/редактировать контрагента</h4>
    <button type="button" class="close" data-dismiss="modal2">&times;</button>
</div>
<div class="modal-body">
    <?php
    if ($model['uuid']) {
        echo Html::hiddenInput("requestUuid", $model['uuid']);
        echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'inn')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'director')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php
    $contragentType = ContragentType::find()->all();
    $items = ArrayHelper::map($contragentType, 'uuid', 'title');
    echo $form->field($model, 'contragentTypeUuid')->widget(Select2::class,
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
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" id="modal2">Закрыть</button>
</div>

<script>
    $("#modal2").click(function () {
            $('#modalContragent').modal('hide');
        }
    );

    $(document).on("beforeSubmit", "#form", function () {
    }).on('submit', function (e) {
        e.preventDefault();
        var form = $('#form');
        $.ajax({
            url: "../contragent/create",
            type: "post",
            data: form.serialize(),
            success: function () {
                $('#modalContragent').modal('hide');
                $.ajax({
                    url: '../contragent/list',
                    data: {
                        type: 1
                    },
                    type: 'post',
                    success: function (data) {
                        var contragents = JSON.parse(data);
                        var select = document.getElementById("request-contragentuuid");
                        select.options.length = 0;
                        for (index in contragents) {
                            select.options[select.options.length] = new Option(contragents[index], index);
                        }
                    }
                });
            },
            error: function () {
            }
        })
    });

</script>
<?php ActiveForm::end(); ?>

