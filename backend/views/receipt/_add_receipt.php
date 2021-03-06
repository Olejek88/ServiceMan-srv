<?php
/* @var $model common\models\Receipt */

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Users;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
        $users = Contragent::find()
            ->where(['IN', 'contragentTypeUuid', [
                ContragentType::CITIZEN,
                ContragentType::ORGANIZATION
            ]])
            ->orderBy('title DESC')
            ->all();
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
            ])->label("Посетитель");
        ?>

        <?= $form->field($model, 'description')->textInput() ?>

        <div class="pole-mg" style="margin: 0 10px;">
            <p style="width: 200px; margin-bottom: 0;">Дата приема</p>
            <?= DateTimePicker::widget([
                'model' => $model,
                'attribute' => 'date',
                'language' => 'ru',
                'size' => 'ms',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd H:ii',
                    'todayHighlight' => true
                ]
            ]);
            ?>
        </div>
        <?php
        $users = Contragent::find()->orderBy('title DESC')
            ->where(['contragentTypeUuid' => [ContragentType::EMPLOYEE, ContragentType::WORKER]])
            ->all();
        $items = ArrayHelper::map($users, 'uuid', 'title');
        echo '<label>Лицо ведущее прием</label><br/>';
        echo Select2::widget(
            [
                'data' => $items,
                'name' => 'contragentUuid',
                'language' => 'ru',
                'options' => [
                        'id' => 'contragent',
                    'placeholder' => 'Лицо ведущее прием'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents' => [
                    "select2:select" => "function(data) { 
                        $.ajax({
                                url: '../contragent/name',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    console.log(data);
                                    $('#receipt-usercheck').val(data);               
                                }
                            });
                  }"]
            ]);
        ?>

        <?= $form->field($model, 'userCheck')->textInput() ?>
        <?= $form->field($model, 'userCheckWho')->textInput() ?>

        <?php
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
        echo $form->field($model, 'userUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
        echo $form->field($model, 'result')->hiddenInput(['value' => "пока нет"])->label(false);
        echo $form->field($model, 'closed')->hiddenInput(['value' => false])->label(false);
        ?>
        <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>

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
