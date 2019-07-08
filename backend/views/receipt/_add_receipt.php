<?php
/* @var $model common\models\Receipt */

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\Users;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options'                => [
            'id'      => 'form'
        ]]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить запись в журнал</h4>
    </div>
    <div class="modal-body">
        <?php
        if ($model['uuid']) {
            echo Html::hiddenInput("receiptUuid", $model['uuid']);
            echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
        } else {
            echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
        }
        ?>

        <?php
        $users = Contragent::find()->orderBy('title DESC')->all();
        $items = ArrayHelper::map($users, 'uuid', 'title');
        echo $form->field($model, 'contragentUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Заявитель'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>

        <?= $form->field($model, 'description')->textInput() ?>

        <div class="pole-mg" style="margin: 0 20px;">
            <p style="width: 200px; margin-bottom: 0;">Дата приема</p>
            <?= DateTimePicker::widget([
                'model' => $model,
                'attribute' => 'date',
                'language' => 'ru',
                'size' => 'ms',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd H:ii',
                    'todayHighlight' => true
                ]
            ]);
            ?>
        </div>
        <?= $form->field($model, 'userCheck')->textInput() ?>

        <?php
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
        echo $form->field($model, 'userUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
        echo $form->field($model, 'result')->hiddenInput(['value' => "пока нет"])->label(false);
        echo $form->field($model, 'closed')->hiddenInput(['value' => false])->label(false);
        ?>
        <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getOid(Yii::$app->user->identity)])->label(false); ?>

    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>

<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "../receipt/new",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modalAdd').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function(e){
        e.preventDefault();
    });
</script>
<?php ActiveForm::end(); ?>
