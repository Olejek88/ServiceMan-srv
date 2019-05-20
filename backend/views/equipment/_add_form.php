<?php
/* @var $equipment \common\models\Equipment */
/* @var string $model_uuid */
/* @var string $reference */

/* @var string $object_uuid */

use common\components\MainFunctions;
use common\models\CriticalType;
use common\models\EquipmentModel;
use common\models\EquipmentStatus;
use common\models\Objects;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
    <h4 class="modal-title">Редактировать оборудование</h4>
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
    echo $form->field($equipment, 'title')->textInput(['maxlength' => true]);

    if ($model_uuid != null) {
        $model = EquipmentModel::find()->where(['uuid' => $model_uuid])->one();
        if ($model) {
            echo $form->field($equipment, 'equipmentModelUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
        }
    } else {
        $equipmentModel = EquipmentModel::find()->all();
        $items = ArrayHelper::map($equipmentModel, 'uuid', 'title');
        echo $form->field($equipment, 'equipmentModelUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите модель..',
                    'style' => ['height' => '42px', 'padding-top' => '10px']
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    echo $form->field($equipment, 'equipmentStatusUuid')->hiddenInput(['value' => EquipmentStatus::WORK])->label(false);
    //echo $form->field($equipment, 'startDate')->hiddenInput(['value' => date("Ymd")])->label(false);
    echo $form->field($equipment, 'tagId')->textInput(['maxlength' => true]);

    echo $form->field($equipment, 'image')->widget(
        FileInput::class,
        ['options' => ['accept' => '*'],]
    );

    echo $form->field($equipment, 'inventoryNumber')->textInput(['maxlength' => true]);
    echo $form->field($equipment, 'serialNumber')->textInput(['maxlength' => true]);

    if ($object_uuid != null) {
        echo $form->field($equipment, 'locationUuid')->hiddenInput(['value' => $object_uuid])->label(false);
        echo Html::hiddenInput("locationUuid", $object_uuid);
    } else {

        $objectType = Objects::find()->all();
        $items = ArrayHelper::map($objectType, 'uuid', 'title');
        $countItems = count($items);
        $isItems = $countItems != 0;

        if ($isItems) {
            echo $form->field($equipment, 'locationUuid')->widget(Select2::class,
                [
                    'data' => $items,
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите объект..',
                        'style' => ['height' => '42px', 'padding-top' => '10px']
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        } else {
            echo $form->field($equipment, 'locationUuid')->dropDownList([
                '00000000-0000-0000-0000-000000000004' => 'Данных нет']);
        }
    }
    ?>
    <div class="pole-mg">
        <p style="width: 300px; margin-bottom: 0;">Дата ввода в эксплуатацию</p>
        <?php echo DatePicker::widget(
            [
                'model' => $equipment,
                'attribute' => 'startDate',
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
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('backend', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: new FormData( this ),
            processData: false,
            contentType: false
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
