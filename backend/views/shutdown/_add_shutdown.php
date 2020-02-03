<?php
/* @var $model common\models\Shutdown */

/* @var $contragents */

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Shutdown;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<div class="equipment-status-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'id' => 'form'
        ]]);
    ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить аварийное отключение</h4>
    </div>
    <div class="modal-body">
        <?php
        if ($model['uuid']) {
            echo Html::hiddenInput("shutdownUuid", $model['uuid']);
            echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
        } else {
            echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
        }

        $items = ArrayHelper::map($contragents, 'uuid', 'title');
        echo $form->field($model, 'contragentUuid',
            ['template' => MainFunctions::getAddButton("/contragent/create")])->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите контрагента..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>
        <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
            <p style="width: 200px; margin-bottom: 0;">Дата начала отключения</p>
            <?= DateTimePicker::widget([
                'model' => $model,
                'attribute' => 'startDate',
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

        <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
            <p style="width: 200px; margin-bottom: 0;">Дата окончания отключения</p>
            <?= DateTimePicker::widget([
                'model' => $model,
                'attribute' => 'endDate',
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
        <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>
        <?php echo $form->field($model, 'comment')->textArea(); ?>
    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
    <script>
        $(document).on("beforeSubmit", "#form", function () {
            $.ajax({
                url: "../shutdown/new",
                type: "post",
                data: $('form').serialize(),
                success: function () {
                    $('#modal_shutdown').modal('hide');
                },
                error: function () {
                }
            })
        }).on('submit', function (e) {
            e.preventDefault();
        });
    </script>
    <?php ActiveForm::end(); ?>
</div>