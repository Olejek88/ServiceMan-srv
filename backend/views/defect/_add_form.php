<?php
/* @var $model */
/* @var $source */
/* @var $equipmentUuid */


use common\components\MainFunctions;
use common\models\DefectType;
use common\models\Equipment;
use common\models\Users;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../defect/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить дефект</h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($model, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);

    echo $form->field($model, 'defectStatus')->hiddenInput(['value' => 0])->label(false);
    echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    $defect_types = DefectType::find()->all();
    $items = ArrayHelper::map($defect_types, 'uuid', 'title');
    echo $form->field($model, 'defectTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_type',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите тип ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo $form->field($model, 'title')->textarea(['rows' => 4, 'style' => 'resize: none;']);
    echo Html::hiddenInput("source", $source);

    if ($equipmentUuid) {
        echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false);
    } else {
        $equipments = Equipment::find()->all();
        $items = ArrayHelper::map($equipments, 'uuid', function ($model) {
            return $model->getFullTitle();
        });
        echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
            [
                'name' => 'kv_type',
                'language' => 'ru',
                'data' => $items,
                'options' => ['placeholder' => 'Выберите элементы ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
    echo $form->field($model, 'userUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);

    echo $form->field($model, 'date')
        ->hiddenInput(['value' => date("Ymd")])
        ->label(false);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('form').serialize(),
            url: "../defect/save",
            success: function () {
                $('#modal_new').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
