<?php
/* @var $equipment Equipment */
/* @var string $model_uuid */
/* @var string $equipmentTypeUuid */
/* @var string $reference */
/* @var $source */

/* @var string $object_uuid */

/* @var string $objectUuid */
/* @var $tagType */

/* @var $tagTypeList */

use common\components\MainFunctions;
use common\components\Tag;
use common\models\Equipment;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Users;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php
$this->registerJs('
    $("#dynamicmodel-tagtype").on("change", function() {
      if ($(this).val() == "' . Tag::TAG_TYPE_DUMMY . '") {
        console.log("type dummy");
        $(".field-equipment-tag").hide();
      } else {
        console.log("type other");
        $(".field-equipment-tag").show();
      }
    });
    $("#dynamicmodel-tagtype").trigger("change");', \yii\web\View::POS_READY);
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '/equipment/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Элементы</h4>
</div>
<div class="modal-body">

    <?php
    if ($equipment['uuid']) {
        echo Html::hiddenInput("equipmentUuid", $equipment['uuid']);
        echo Html::hiddenInput("reference", $reference);

        echo $form->field($equipment, 'uuid')
            ->hiddenInput(['value' => $equipment['uuid']])
            ->label(false);
    } else {
        echo $form->field($equipment, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
    }
    echo $form->field($equipment, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($equipment, 'title')->textInput(['maxlength' => true]);
    echo $form->field($equipment, 'equipmentStatusUuid')->hiddenInput(['value' => EquipmentStatus::WORK])->label(false);

    echo $form->field($tagType, 'tagType')->dropDownList($tagTypeList)->label('Тип метки');
    echo $form->field($equipment, 'tag')->textInput(['maxlength' => true]);

    echo $form->field($equipment, 'serial')->textInput(['maxlength' => true]);
    echo $form->field($equipment, 'period')->textInput(['maxlength' => true]);
    echo Html::hiddenInput("source", $source);
    echo Html::hiddenInput("type", "equipment");

    if ($equipment['uuid']) {
        echo $form->field($equipment, 'objectUuid')->hiddenInput(['value' => $equipment['objectUuid']])->label(false);
    } else {
        echo $this->render('../object/_select_object_subform', ['form' => $form]);
        echo $form->field($equipment, 'objectUuid')->widget(\kartik\widgets\Select2::class,
            ['id' => 'objectUuid',
                'name' => 'objectUuid',
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите объект..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }

    if ($equipment['equipmentTypeUuid']) {
        echo $form->field($equipment, 'equipmentTypeUuid')->hiddenInput(['value' => $equipment['equipmentTypeUuid']])->label(false);
    } else {
        $equipmentType = EquipmentType::find()->all();
        $items = ArrayHelper::map($equipmentType, 'uuid', 'title');
        echo $form->field($equipment, 'equipmentTypeUuid')->widget(Select2::class,
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
    }
    ?>

    <?php echo $form->field($equipment, 'inputDate')->widget(DatePicker::class,
        [
            'language' => 'ru',
            'size' => 'ms',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ]
    );
    ?>

    <?php echo $form->field($equipment, 'testDate')->widget(DatePicker::class,
        [
            'language' => 'ru',
            'size' => 'ms',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ]
    );
    ?>

    <?php echo $form->field($equipment, 'replaceDate')->widget(DatePicker::class,
        [
            'model' => $equipment,
            'attribute' => 'replaceDate',
            'language' => 'ru',
            'size' => 'ms',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ]
    );
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
            url: "../equipment/save",
            success: function () {
                $('#modalAddEquipment').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
