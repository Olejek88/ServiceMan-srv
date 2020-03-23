<?php
use common\models\Equipment;
use common\models\Request;
use common\models\TaskTemplate;
use common\models\TaskVerdict;
use common\models\Users;
use common\models\UserSystem;
use common\models\WorkStatus;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
use kartik\widgets\DepDrop;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model common\models\Task */
/* @var $equipment Equipment */
/* @var $request Request */
/* @var $authorUuid */
/* @var $equipments Equipment[] */
/* @var $userSystem UserSystem[] */
/* @var $taskTemplates TaskTemplate[] */
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => "../task/add-task",
    'options' => [
        'id' => 'form2'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить задачу</h4>
</div>
<div class="modal-body">
    <?php
    if ($equipment != null) {
        echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $equipment->uuid])->label(false);
    } else {
        echo '<label class="control-label" for="objectsUuid">Объект / инженерная система</label>';
        echo Select2::widget(
            [
                'id' => 'objectsUuid',
                'name' => 'objectsUuid',
                'language' => 'ru',
                'data' => $objects,
                'value' => $request != null ? $request->objectUuid : '',
                'options' => [
                    'placeholder' => 'Выберите объект/инженерную систему...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);

        $eqUuid = isset($equipments[0]) ? $equipments[0]->uuid : null;
        echo '<br/>';
        $placeHolder = 'Выберите элемент...';
        echo $form->field($model, 'equipmentUuid')->widget(DepDrop::class, [
            'data' => ArrayHelper::map($equipments, 'uuid', 'title'),
            'language' => 'ru',
            'options' => [
                'placeholder' => $placeHolder,
                'value' => $eqUuid,
            ],
            'pluginOptions' => [
                'depends' => ['objectsUuid'],
                'url' => Url::to(['//task/get-equipments']),
                'placeholder' => $placeHolder,
            ],
        ]);
    }
    ?>

    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>
    <?php echo $form->field($model, 'workStatusUuid')->hiddenInput(['value' => WorkStatus::NEW])->label(false); ?>
    <?php echo $form->field($model, 'taskVerdictUuid')->hiddenInput(['value' => TaskVerdict::NOT_DEFINED])->label(false); ?>
    <?php
    echo $form->field($model, 'authorUuid')->hiddenInput(['value' => $authorUuid])->label(false);
    ?>
    <?php if ($request != null) echo Html::hiddenInput("requestUuid", $request->uuid); ?>

    <?php
    echo '<label class="control-label">Исполнитель</label>';
    if ($equipment != null) {
        echo Select2::widget(
            [
                'id' => 'userUuid',
                'name' => 'userUuid',
                'language' => 'ru',
                'data' => $userSystem,
                'options' => ['placeholder' => 'Выберите исполнителя ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } else {
        echo '<br/>';
        $placeHolder = 'Выберите исполнителя...';
        echo DepDrop::widget([
            'id' => 'userUuid',
            'name' => 'userUuid',
            'language' => 'ru',
            'data' => $userSystem,
            'options' => ['placeholder' => $placeHolder],
            'pluginOptions' => [
                'placeholder' => $placeHolder,
                'depends' => ['task-equipmentuuid'],
                'url' => Url::to(['//task/get-user-system'])
            ],
        ]);
    }

    if ($equipment != null) {
        echo $form->field($model, 'taskTemplateUuid')->widget(Select2::class, [
            'data' => $taskTemplates,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите шаблон задачи...',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    } else {
        echo '<br/>';
        $placeHolder = 'Выберите шаблон задачи...';
        echo $form->field($model, 'taskTemplateUuid')->widget(DepDrop::class, [
            'data' => $taskTemplates,
            'language' => 'ru',
            'options' => [
                'placeholder' => $placeHolder,
            ],
            'pluginOptions' => [
                'placeholder' => $placeHolder,
                'depends' => ['task-equipmentuuid'],
                'url' => Url::to(['//task/get-task-template'])
            ],
        ]);
    }
    ?>

    <?php
    if ($request == null)
        echo $form->field($model, 'comment')
            ->textarea(['rows' => 4, 'style' => 'resize: none;']);
    ?>

    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
        <p style="width: 0; margin-bottom: 0; width: 300px">Дата начала работ</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'taskDate',
            'language' => 'ru',
            'size' => 'ms',
            'value' => date("Y-m-d H:i"),
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
    <?php
    echo Html::textInput("errors", "", ['readonly' => 'readonly', 'style' => 'width:100%', 'id' => 'errors', 'name' => 'errors'])
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form2", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        var me = $('button.btn.btn-success', e.target);
        e.preventDefault();
        me.prop('disabled', true).removeClass('enabled').addClass('disabled');
        var form = $('#form2');
        $.ajax({
            url: "../task/add-task",
            type: "post",
            data: form.serialize(),
            success: function () {
                $('#modalTask').modal('hide');
                window.location.reload();
            },
            error: function (result) {
                //alert(result.statusText);
            },
            complete: function () {
                me.prop('disabled', false).removeClass('disabled').addClass('enabled');
            }
        })
    });

</script>
<?php ActiveForm::end(); ?>
