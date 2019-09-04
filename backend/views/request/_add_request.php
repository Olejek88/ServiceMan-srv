<?php
/* @var $model common\models\Request
 * @var $receiptUuid string
 * @var $source string
 */

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Equipment;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Task;
use common\models\Users;
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
    <h4 class="modal-title">Добавить/редактировать заявку</h4>
</div>
<div class="modal-body">
    <?php
    if ($model['uuid']) {
        echo Html::hiddenInput("requestUuid", $model['uuid']);
        echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php
    echo Html::hiddenInput("receiptUuid", $receiptUuid);
    echo $form->field($model, 'type')->widget(Select2::class,
        [
            'data' => [0 => "Бесплатная заявка", 1 => "Платная заявка"],
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    if ($source == 'table') {
        $users = Contragent::find()
            ->where(['contragentTypeUuid' => [ContragentType::ORGANIZATION, ContragentType::CITIZEN]])
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
                'pluginEvents' => [
                    "select2:select" => "function(data) { 
                        $.ajax({
                                url: '../contragent/phone',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    console.log(data);
                                    $('#phoneNumber').val(data);               
                                }
                            });
                        $.ajax({
                                url: '../contragent/address',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    console.log(data);
                                    $('#request-objectuuid').val(data).trigger('change');
                                }
                            });
                  }"]
            ]);
        echo '<label>Номер телефона заявителя</label></br>';
        echo Html::textInput("phoneNumber", '', ['id' => 'phoneNumber']);
    } else {
        echo $form->field($model, 'contragentUuid')->hiddenInput(['value' => Contragent::DEFAULT_CONTRAGENT])->label(false);
    }
    echo '</br>';

    if (!$model->objectUuid) {
        echo $this->render('../object/_select_object_subform', ['form' => $form]);
        echo $form->field($model, 'objectUuid')->widget(Select2::class,
            ['id' => 'objectUuid',
                'name' => 'objectUuid',
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите объект..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } else {
        echo $form->field($model, 'objectUuid')->hiddenInput(['value' => $model['objectUuid']])->label(false);
    }
    echo $form->field($model, 'result')->hiddenInput(['value' => 'Нет результата'])->label(false);

    echo $form->field($model, 'comment')->textInput();
    ?>

    <?php
    $type = RequestType::find()
        ->innerJoinWith('taskTemplate')
        ->where(['task_template.oid' => Users::getCurrentOid()])
        ->all();
    $items = ArrayHelper::map($type, 'uuid', 'title');
    echo $form->field($model, 'requestTypeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..',
                'value' => RequestType::GENERAL
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    if (!$model->equipmentUuid) {
        $equipments = Equipment::find()->all();
        $items = ArrayHelper::map($equipments, 'uuid', function ($equipment) {
            return $equipment->getFullTitle();
        });
        echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите элементы..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } else {
        echo $form->field($model, 'equipmentUuid')->hiddenInput(['value' => $model['equipmentUuid']])->label(false);
    }
    ?>


    <?php
    if ($model->objectUuid && $model->equipmentUuid && false) {
        $tasks = Task::find()->all();
        $items = ArrayHelper::map($tasks, 'uuid', 'taskTemplate.title');
        echo $form->field($model, 'taskUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Задача'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    ?>

    <?php
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
    echo $form->field($model, 'authorUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
    echo $form->field($model, 'requestStatusUuid')->hiddenInput(['value' => RequestStatus::NEW_REQUEST])->label(false);
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>

<script>
    $(document).on("beforeSubmit", "#form", function () {
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "../request/new",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modalRequest').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
