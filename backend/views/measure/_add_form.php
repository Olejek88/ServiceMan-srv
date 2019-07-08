<?php

/* @var $measure Measure */

/* @var string $equipmentUuid */

use common\components\MainFunctions;
use common\models\Measure;
use common\models\MeasureType;
use common\models\Users;
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
    ],
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить измерение</h4>
</div>
<div class="modal-body">
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'equipmentUuid')
        ->hiddenInput(['value' => $equipmentUuid])->label(false);

    $measureType = MeasureType::find()->all();
    $items = ArrayHelper::map($measureType, 'uuid', 'title');
    echo $form->field($model, 'measureTypeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Тип измерения..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?php
/*    $users = Users::find()->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');
    echo $form->field($model, 'userUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите пользователя..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
*/
    ?>
    <?php
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
    echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($model, 'userUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
    ?>
    <div class="pole-mg" style="margin: 0 -5px 20px -5px;">
        <p style="width: 200px; margin-bottom: 0;">Дата измерения</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'date',
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

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

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
            url: "../measure/save",
            success: function () {
                $('#modalMeasure').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>

