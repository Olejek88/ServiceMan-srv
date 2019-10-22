<?php
/* @var $equipment common\models\Equipment
 * @var $equipment_id
 */

use common\models\Equipment;
use common\models\StageType;
use common\models\TaskTemplate;
use common\models\TaskTemplateEquipmentType;
use common\models\TaskType;
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
    <h4 class="modal-title">Выбрать шаблон задачи</h4>
</div>
<div class="modal-body">
    <?php
    if ($_POST["equipment_id"])
        $equipment_id = $_POST["equipment_id"];
    $equipment = Equipment::find()->where(['_id' => $equipment_id])->one();
    if ($equipment) {
        $taskTemplate = TaskTemplateEquipmentType::find()
            ->joinWith('taskTemplate')
            ->where(['equipmentTypeUuid' => $equipment['equipmentTypeUuid']])
            ->andWhere(['or',
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_CONTROL],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_NOT_PLAN_TO],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_MEASURE],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_REPAIR],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_INSTALL],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_CURRENT_REPAIR],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_NOT_PLANNED_CHECK],
                ['task_template.taskTypeUuid' => TaskType::TASK_TYPE_CURRENT_CHECK]])
            ->orderBy('task_template.taskTypeUuid')
            ->all();
        $items = ArrayHelper::map($taskTemplate, 'taskTemplate.uuid', function ($model) {
            return $model['taskTemplate']['taskType']['title'] . ' :: ' . $model['taskTemplate']['title'];
        });
    } else {
        $taskTemplates = TaskTemplate::find()->all();
        $items = ArrayHelper::map($taskTemplates, 'uuid', 'title');
    }
    echo Select2::widget(
        [
            'id' => 'taskTemplateUuid',
            'name' => 'taskTemplateUuid',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите шаблон ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo '<label class="control-label">Период (дн.)</label><br/>';
    echo Html::textInput("period");
    echo Html::hiddenInput("equipment_uuid", $equipment['uuid']);
    ?>
    <label>Дата отсчета</label>
    <div class="pole-mg" style="margin: 2px 2px 2px 5px;">
        <?= DatePicker::widget([
            'id' => 'last_date',
            'name' => 'last_date',
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
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "choose",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                console.log("success?!");
                $('#modalAddOperation').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function (e) {
        e.preventDefault();
    });

</script>
<?php ActiveForm::end(); ?>
