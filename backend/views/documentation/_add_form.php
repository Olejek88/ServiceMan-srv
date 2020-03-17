<?php
/* @var $documentation */
/* @var $equipmentUuid */
/* @var $houseUuid */
/* @var $equipmentTypeUuid */

/* @var $source */
/* @var $equipmentType */

use common\components\MainFunctions;
use common\models\DocumentationType;
use common\models\Equipment;
use common\models\House;
use common\models\Users;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../documentation/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить документацию</h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($documentation, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    echo $form->field($documentation, 'title')->textInput(['maxlength' => true]);

    if (isset($equipmentType)) {
        $documentationType_selected = DocumentationType::find()
            ->where(['_id' => $equipmentType])
            ->orderBy('title')
            ->one();
        if ($documentationType_selected) {
            echo $form->field($documentation, 'documentationTypeUuid')->
            hiddenInput(['value' => $documentationType_selected['uuid']])->label(false);
        }
    }
    if (!isset($documentationType_selected)) {
        $documentationTypes = DocumentationType::find()
            ->orderBy('title')
            ->all();
        $items = ArrayHelper::map($documentationTypes, 'uuid', 'title');
        echo $form->field($documentation, 'documentationTypeUuid')->widget(Select2::class,
            [
                'name' => 'kv_types',
                'language' => 'ru',
                'data' => $items,
                'options' => ['placeholder' => 'Выберите тип  ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false);
    }
    echo $form->field($documentation, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);

    echo $form->field($documentation, 'required')->hiddenInput(['value' => 0])->label(false);

    if ($equipmentUuid && $equipmentTypeUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => $equipmentTypeUuid])->label(false);
        echo $form->field($documentation, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false);
    }
    if ($equipmentTypeUuid && !$equipmentUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => $equipmentTypeUuid])->label(false);
    }
    if (!$equipmentTypeUuid && $equipmentUuid) {
        echo $form->field($documentation, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false);
    }
    if (!$equipmentTypeUuid && !$equipmentUuid && !$houseUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => null])->label(false);
        $equipment = Equipment::find()
            ->where(['deleted' => false])
            ->orderBy('objectUuid')
            ->all();
        $items = ArrayHelper::map($equipment, 'uuid', function ($model) {
            return $model['equipmentType']['title'] . ' (' . $model['object']['house']['street']['title'] . ', ' .
                $model['object']['house']['number'] . ', ' .
                $model['object']['title'] . ')';
        });
        echo $form->field($documentation, 'equipmentUuid')->widget(Select2::class,
            [
                'name' => 'kv_type',
                'language' => 'ru',
                'data' => $items,
                'options' => ['placeholder' => 'Выберите элемент ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false);
    }
    if ($houseUuid) {
        echo $form->field($documentation, 'equipmentTypeUuid')->hiddenInput(['value' => null])->label(false);
        echo $form->field($documentation, 'equipmentUuid')->hiddenInput(['value' => null])->label(false);
        echo $form->field($documentation, 'houseUuid')->hiddenInput(['value' => $houseUuid])->label(false);
        /*        $houses = House::find()
                    ->orderBy('streetUuid')
                    ->all();
                $items = ArrayHelper::map($houses, 'uuid', function ($model) {
                    return $model['street']['title'] . ', д.' . $model['number'];
                });
                echo $form->field($documentation, 'houseUuid')->widget(Select2::class,
                    [
                        'name' => 'kv_type',
                        'language' => 'ru',
                        'data' => $items,
                        'options' => [
                            'placeholder' => 'Выберите дом ...',
                            'value' => $houseUuid
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(false);*/
    }

    if (isset($source)) {
        echo Html::hiddenInput("source", $source);
    }

    echo $form->field($documentation, 'docFile')
        ->widget(FileInput::class, ['options' => ['accept' => '*']]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: new FormData(this),
            url: "../documentation/save",
            success: function () {
                $('#modalAddDocumentation').modal('hide');
                window.location.reload();
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
