<?php
/* @var $task common\models\Task  */
/* @var $equipmentUuid */
/* @var $requestUuid */

use common\components\MainFunctions;
use common\models\Equipment;
use common\models\TaskTemplate;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\WorkStatus;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
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
    <h4 class="modal-title">Добавить задачу !</h4>
</div>
<div class="modal-body">
    <?php
/*    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }*/
    ?>

    <?php echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $equipmentUuid])->label(false); ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false); ?>
    <?php echo $form->field($model, 'workStatusUuid')->hiddenInput(['value' => WorkStatus::NEW])->label(false); ?>
    <?php echo $form->field($model, 'taskVerdictUuid')->hiddenInput(['value' => TaskVerdict::NOT_DEFINED])->label(false); ?>
    <?php
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
        echo $form->field($model, 'authorUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
        ?>
    <?php if (isset($requestUuid)) echo Html::hiddenInput("requestUuid", $requestUuid); ?>

    <?php
    $users = Users::find()->where(['<>','name','sUser'])->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');
    echo '<label class="control-label">Исполнитель</label>';
    echo Select2::widget(
        [
            'id' => 'userUuid',
            'name' => 'userUuid',
            'language' => 'ru',
            'data' => $items,
            'options' => ['placeholder' => 'Выберите исполнителя ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    $taskTemplate = TaskTemplate::find()->all();
    $items = ArrayHelper::map($taskTemplate, 'uuid', 'title');
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);
    ?>

    <?php
    echo $form->field($model, 'comment')
        ->textarea(['rows' => 4, 'style' => 'resize: none;'])
    ?>

    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
        <p style="width: 0; margin-bottom: 0;">Дата назначения</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'taskDate',
            'language' => 'ru',
            'size' => 'ms',
            'clientOptions' => [
                'autoclose' => true,
                'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                'todayBtn' => true
            ]
        ]);
        ?>
    </div>

    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
        <p style="width: 0; margin-bottom: 0;">Срок</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'deadlineDate',
            'language' => 'ru',
            'size' => 'ms',
            'clientOptions' => [
                'autoclose' => true,
                'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                'todayBtn' => true
            ]
        ]);
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
            url: "../task/add-task",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                console.log("success?!");
                $('#modalTask').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function (e) {
        e.preventDefault();
    });

</script>
<?php ActiveForm::end(); ?>
