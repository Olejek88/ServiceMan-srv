<?php
/* @var $house
 * @var $source
 * @var $streetUuid
 */
use common\components\MainFunctions;
use common\models\HouseStatus;use common\models\HouseType;use common\models\Users;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../object/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Дом</h4>
</div>
<div class="modal-body">
    <?php
    if ($house['uuid']) {
        echo Html::hiddenInput("houseUuid", $house['uuid']);
        echo $form->field($house, 'uuid')
            ->hiddenInput(['value' => $house['uuid']])
            ->label(false);
    } else {
        echo $form->field($house, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
        echo $form->field($house, 'streetUuid')->hiddenInput(['value' => $streetUuid])->label(false);
        echo $form->field($house, 'houseStatusUuid')->hiddenInput(['value' => HouseStatus::HOUSE_STATUS_OK])->label(false);
    }
    echo $form->field($house, 'oid')->hiddenInput(['value' => Users::ORGANISATION_UUID])->label(false);
    echo $form->field($house, 'number')->textInput(['maxlength' => true]);

    echo Html::hiddenInput("type", "house");
    echo Html::hiddenInput("source", $source);

    $types = HouseType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    echo $form->field($house, 'houseTypeUuid')->widget(\kartik\widgets\Select2::class,
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

    echo '<label class="control-label" style="font-weight: bold">Для МКД</label></br>';
    echo '<label class="control-label">Квартир</label>&nbsp;&nbsp;';
    echo Html::textInput("flats");
    echo '</br>';
    echo '<label class="control-label">Этажей</label>&nbsp;&nbsp;';
    echo Html::textInput("stages");
    echo '</br>';
    echo '<label class="control-label">Подъездов</label>&nbsp;&nbsp;';
    echo Html::textInput("entrances");
    echo '</br>';
    echo '<label class="control-label">Тип плит / наличие газа</label>';
    $types = [
        '0' => 'Электричество',
        '1' => 'Газ'
    ];
    echo Select2::widget(
        [
                'name' => 'energy',
            'data' => $types,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo '</br>';
    echo Html::checkbox('lift',true,['label' => 'Лифт']);
    echo '</br>';
    echo Html::checkbox('water_counter',true,['label' => 'Квартирные счетчики воды']);
    echo '</br>';
    echo Html::checkbox('balcony',true,['label' => 'Балконы']);
    echo '</br>';
    echo Html::checkbox('water_system',true,['label' => 'Система водоснабжения']);
    echo '</br>';
    echo Html::checkbox('yard',true,['label' => 'Придомовая территория']);
    echo '</br>';
    echo Html::checkbox('internet',true,['label' => 'Интернет']);
    echo '</br>';
    echo Html::checkbox('tv',true,['label' => 'ТВ']);
    echo '</br>';
    echo Html::checkbox('domophones',true,['label' => 'Домофоны']);
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
        $.ajax({
            type: "post",
            data: new FormData(this),
            processData: false,
            contentType: false
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
