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
use yii\web\View;

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
    $("#dynamicmodel-tagtype").trigger("change");', View::POS_READY);
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '/equipment/save',
    'options' => [
        'id' => 'form-add-equipment',
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

    if (isset($objectUuid)) {
        echo $form->field($equipment, 'objectUuid')->hiddenInput(['value' => $objectUuid])->label(false);
    } elseif ($equipment['objectUuid']) {
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

    if (isset($equipmentTypeUuid)) {
        echo $form->field($equipment, 'equipmentTypeUuid')->hiddenInput(['value' => $equipmentTypeUuid])->label(false);
    } elseif ($equipment['equipmentTypeUuid']) {
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
    if ($(document).data('_add_form_eq') === true) {
    } else {
        $(document).data('_add_form_eq', true);
        $(document)
            .on("beforeSubmit", "#form-add-equipment", function (e) {
                e.preventDefault();
            })
            .on('submit', "#form-add-equipment", function (e) {
                e.preventDefault();
                var form = $(this);
                if (form.data('submited') === true) {
                } else {
                    form.data('submited', true);
                    $.ajax({
                        url: "../equipment/save",
                        type: "post",
                        data: form.serialize(),
                        success: function () {
                            $('#modalAddEquipment').modal('hide');
                        },
                        error: function (error) {
                            // когда на ajax запрос отвечают редиректом, генерируется ошибка
                            if (error.status !== 302) {
                                // если это не редирект, включаем возможность повторной отправки формы
                                form.data('submited', false);
                            }

                            if (error.status === 302) {
                                // если редирект, считаем что всё в порядке
                                $('#modalAddEquipment').modal('hide');
                            }
                        }
                    });
                }
            });
    }
</script>
<?php ActiveForm::end(); ?>
