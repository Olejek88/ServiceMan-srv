<?php
/* @var $model common\models\Request
 * @var $receiptUuid string
 * @var $source string
 */

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Equipment;
use common\models\Objects;
use common\models\ObjectType;
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

    if ($source == 'table') {
        $users = Contragent::find()
            ->where(['contragentTypeUuid' => ContragentType::ORGANIZATION])
            ->orWhere(['contragentTypeUuid' => ContragentType::CITIZEN])
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
        $objects = Objects::find()
            ->where(['objectTypeUuid' => ObjectType::OBJECT_TYPE_FLAT])
            ->orWhere(['objectTypeUuid' => ObjectType::OBJECT_TYPE_COMMERCE])
            ->all();
        $items = ArrayHelper::map($objects, 'uuid', function ($object) {
            return $object['house']['street']->title . ', ' . $object['house']->number . ', ' . $object['objectType']['title'] .' '. $object['title'];
        });
        echo $form->field($model, 'objectUuid',
            ['template' => MainFunctions::getAddButton("/object/create")])->widget(Select2::class,
            [
                'data' => $items,
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

    echo $form->field($model, 'comment')->textInput();

    /*        $objectContragent = 0;
            if ($model['objectUuid'])
                $objectContragent = ObjectContragent::find()->where(['objectUuid' => $model['objectUuid']])->one();
            $contragents = Contragent::find()->all();
            $items = ArrayHelper::map($contragents, 'uuid', 'title');
            echo $form->field($model, 'contragentUuid',
                ['template' => MainFunctions::getAddButton("/contragent/create")])->widget(Select2::class,
                [
                    'data' => $items,
                    'value' => $objectContragent,
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите исполнителя..'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            */ ?>

    <?php
    $type = RequestType::find()
        ->innerJoinWith('taskTemplate')
        ->where(['task_template.oid' => Users::getCurrentOid()])
        ->all();
    $items = ArrayHelper::map($type, 'uuid', 'title');
    echo $form->field($model, 'requestTypeUuid',
        ['template' => MainFunctions::getAddButton("/request-type/create")])->widget(Select2::class,
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
        echo $form->field($model, 'equipmentUuid',
            ['template' => MainFunctions::getAddButton("/equipment/create")])->widget(Select2::class,
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
