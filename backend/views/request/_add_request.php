<?php
/* @var $model common\models\Request
 * @var $receiptUuid string
 * @var $source string
 * @var $equipmentUuid string
 * @var $phone string
 * @var $path string
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

if (isset($_GET["equipmentUuid"]))
    $equipmentUuid = $_GET["equipmentUuid"];
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
    <table style="width: 100%">
        <tr>
            <td style="width: 48%; vertical-align: top">
                <?php
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
                        ->andWhere(['deleted' => 0])
                        ->orderBy('title DESC')
                        ->all();
                    $items = ArrayHelper::map($users, 'uuid', 'title');
                    if ($model['uuid']) {
                        $template = '{label}<div class="input-group">{input}</div>';
                    } else {
                        $template = '{label}<div class="input-group">{input} <span class="input-group-btn">' .
                            Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                                ['../contragent/form'],
                                [
                                    'class' => 'btn btn-success',
                                    'title' => 'Добавить контрагента',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalContragent',
                                ]) .
                            '</span></div>';
                    }
                    echo $form->field($model, 'contragentUuid',
                        ['template' => $template])->widget(Select2::class,
                        ['data' => $items,
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
                                            var obj = JSON.parse(data);
                                            console.log(obj.city);
                                            $('#request-cityuuid').val(obj.city).trigger('change');
                                            refreshStreets(obj.city);
                                            console.log(obj.street);
                                            $('#request-streetuuid').val(obj.street).trigger('change');
                                            console.log(obj.house);
                                            $('#request-houseuuid').val(obj.house).trigger('change');
                                            console.log(obj.object);
                                            $('#request-objectuuid').val(obj.object).trigger('change');
                                        }
                                    });
                            }"]
                        ]);
                    echo '<label>Номер телефона заявителя</label></br>';
                    echo Html::textInput("phoneNumber", $phone, ['id' => 'phoneNumber']);
                } else {
                    echo $form->field($model, 'contragentUuid')->hiddenInput(['value' => Contragent::DEFAULT_CONTRAGENT])->label(false);
                }
                echo '</br>';

                if (!$model->objectUuid) {
                    echo $this->render('../object/_select_object_subform');
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
                        ])->label("Квартира/помещение");
                } else {
                    echo $form->field($model, 'objectUuid')->hiddenInput(['value' => $model['objectUuid']])->label(false);
                }
                echo $form->field($model, 'result')->hiddenInput(['value' => 'Нет результата'])->label(false);
                ?>
            </td>
            <td style="width: 4%"></td>
            <td style="width: 48%; vertical-align: top">
                <?php
                echo $form->field($model, 'comment')->textInput();
                $defaultRequestType = RequestType::find()->where(['title' => 'Другой характер обращения'])->one();
                if ($model['requestTypeUuid'])
                    $value = $model['requestTypeUuid'];
                else if ($defaultRequestType)
                    $value = $defaultRequestType['uuid'];
                $type = RequestType::find()
                    ->where(['oid' => Users::getCurrentOid()])
                    ->all();
                $items = ArrayHelper::map($type, 'uuid', 'title');
                echo $form->field($model, 'requestTypeUuid')->widget(Select2::class,
                    [
                        'data' => $items,
                        'language' => 'ru',
                        'options' => [
                            'placeholder' => 'Выберите тип..',
                            'value' => $value
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                ?>

                <?php
                if ($source == 'table') {
                    echo $this->render('../object/_select_equipment_subform', ['equipmentUuid' => $model['equipmentUuid']]);
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

                if ($model['uuid']) {
                    echo Html::hiddenInput("requestUuid", $model['uuid']);
                    echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
                } else {
                    echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
                }
                echo Html::hiddenInput("receiptUuid", $receiptUuid);
                echo Html::textInput("errors", "", ['readonly' => 'readonly', 'style' => 'width:100%', 'id' => 'errors', 'name' => 'errors'])
                ?>
            </td>
        </tr>
    </table>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false); ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>

<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        var form = $('#form');
        $.ajax({
            url: "../request/new",
            type: "post",
            data: form.serialize(),
            success: function (ret) {
                if (ret.length > 5) {
                    $('#errors').val(ret);
                } else {
                    $('#modalRequest').modal('hide');
                    window.location.reload();
                }
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>

<div class="modal remote fade" id="modalContragent">
    <div class="modal-dialog" style="width: 600px; height: 650px">
        <div class="modal-content loader-lg" id="modalContragentContent">
        </div>
    </div>
</div>
