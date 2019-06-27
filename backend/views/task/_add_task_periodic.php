<?php
/* @var $task common\models\TaskTemplateEquipment  */
/* @var $equipmentUuid */
/* @var $type_uuid */
/* @var $requestUuid */

use common\components\MainFunctions;
use common\models\Task;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipmentType;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\WorkStatus;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\date\DatePicker;
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
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить периодическую задачу</h4>
</div>
<div class="modal-body">
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false); ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false); ?>

    <?php
    $taskTemplate = TaskTemplateEquipmentType::find()->where(['equipmentTypeUuid' => $type_uuid])->all();
    $items = ArrayHelper::map($taskTemplate, 'taskTemplateUuid', function ($data) {
        return $data['taskTemplate']['taskType']['title'].' : '.$data['taskTemplate']['title'];
    });
    echo $form->field($model, 'taskTemplateUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Шаблон задачи'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($model, 'period')->textInput(['maxlength' => true]);
    ?>

    <label>Дата первого создания</label>
    <div class="pole-mg" style="margin: 2px 2px 2px 5px;">
        <?= DatePicker::widget([
            'attribute' => 'last_date',
            'model' => $model,
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ])
        ?>
    </div>

</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#dynamic-form", function ($e) {
        console.log($e);
    }).on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: "../task/new-periodic",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                console.log("success?!");
                $('#modalAddPeriodicTask').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
