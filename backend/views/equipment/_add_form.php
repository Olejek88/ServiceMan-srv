<?php
/* @var $equipment Equipment */
/* @var string $model_uuid */
/* @var string $equipmentTypeUuid */
/* @var string $reference */
/* @var $source */

/* @var string $object_uuid */
/* @var string $objectUuid */

use common\components\MainFunctions;
use common\models\Equipment;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Objects;
use common\models\Users;
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
    <h4 class="modal-title">Оборудование</h4>
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
    echo $form->field($equipment, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false);
    echo $form->field($equipment, 'title')->textInput(['maxlength' => true]);
    echo $form->field($equipment, 'equipmentStatusUuid')->hiddenInput(['value' => EquipmentStatus::WORK])->label(false);
    echo $form->field($equipment, 'tag')->textInput(['maxlength' => true]);

    echo '<label class="control-label">Фотография</label>';
    echo FileInput::widget([
        'name' => 'image',
        'options' => ['accept' => '*']
    ]);

    echo $form->field($equipment, 'serial')->textInput(['maxlength' => true]);
    echo $form->field($equipment, 'period')->textInput(['maxlength' => true]);
    echo Html::hiddenInput("source", $source);
    echo Html::hiddenInput("type", "equipment");


    if (isset($objectUuid)) {
        echo $form->field($equipment, 'objectUuid')->hiddenInput(['value' => $objectUuid])->label(false);
    } else {
        $object = Objects::find()->all();
        $items = ArrayHelper::map($object, 'uuid', function ($model) {
            return $model['house']['street']->title . ', ' . $model['house']->number . ', ' . $model['title'];
        });
        echo $form->field($equipment, 'objectUuid')->widget(Select2::class,
            [
                'data' => $items,
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

    <div class="pole-mg">
        <p style="width: 300px; margin-bottom: 0;">Дата поверки</p>
        <?php echo DatePicker::widget(
            [
                'model' => $equipment,
                'attribute' => 'testDate',
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
    <div class="pole-mg">
        <p style="width: 300px; margin-bottom: 0;">Дата замены</p>
        <?php echo DatePicker::widget(
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
