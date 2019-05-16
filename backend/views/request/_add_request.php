<?php
/* @var $model common\models\Request */

use common\components\MainFunctions;
use common\models\Contragent;
use common\models\Equipment;
use common\models\Objects;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Task;
use common\models\Users;
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
        <h4 class="modal-title">Добавить заявку</h4>
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
        $users = Contragent::find()->orderBy('title DESC')->all();
        $items = ArrayHelper::map($users, 'uuid', 'title');
        echo $form->field($model, 'userUuid')->widget(Select2::class,
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

        <?php
        $contragents = Contragent::find()->all();
        $items = ArrayHelper::map($contragents, 'uuid', 'title');
        echo $form->field($model, 'contragentUuid',
            ['template' => MainFunctions::getAddButton("/contragent/create")])->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите исполнителя..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>

        <?php
        $type = RequestType::find()->all();
        $items = ArrayHelper::map($type, 'uuid', 'title');
        echo $form->field($model, 'requestTypeUuid',
            ['template' => MainFunctions::getAddButton("/request-type/create")])->widget(Select2::class,
            [
                'data' => $items,
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
        $equipments = Equipment::find()->all();
        $items = ArrayHelper::map($equipments, 'uuid', 'title');
        echo $form->field($model, 'equipmentUuid',
            ['template' => MainFunctions::getAddButton("/equipment/create")])->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите оборудование..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>

        <?php
        $objects  = Objects::find()->all();
        $items = ArrayHelper::map($objects,'uuid','title');
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
        ?>

        <?php
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
        ?>

        <?php
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
        echo $form->field($model, 'authorUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
        echo $form->field($model, 'requestStatusUuid')->hiddenInput(['value' => RequestStatus::NEW_REQUEST])->label(false);
        ?>
        <?php echo $form->field($model, 'oid')->hiddenInput(['value' => Users::ORGANISATION_UUID])->label(false); ?>

        <?= $form->field($model, 'comment')->textInput() ?>
    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>

<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "../request/new",
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
