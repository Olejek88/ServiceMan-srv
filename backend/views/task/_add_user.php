<?php

use common\models\TaskUser;
use common\models\Users;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $taskUuid */
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
    <h4 class="modal-title">Назначить пользователей</h4>
</div>
<div class="modal-body">

    <?php
    $userTasks = TaskUser::find()
        ->where(['taskUuid' => $taskUuid])
        ->all();
    echo '<label class="control-label">Убрать исполнителя</label>';
    echo '</br>';
    foreach ($userTasks as $userTask) {
        echo Html::checkbox('user-' . $userTask['user']['_id'], false, ['label' => $userTask['user']['name']]);
        echo '</br>';
    }
    $users = Users::find()->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');

    echo Html::hiddenInput('taskUuid',$taskUuid);

    echo '</br>';
    echo '<label class="control-label">Добавить исполнителя</label>';
    $users = Users::find()->where(['!=', 'uuid', Users::USER_SERVICE_UUID])->all();
    $items = ArrayHelper::map($users, 'uuid', 'name');
    try {
        echo Select2::widget(
            [
                'data' => $items,
                'name' => 'userAdd',
                'language' => 'ru',
                'options' => [
                    'id' => 'user-id',
                    'placeholder' => 'Выберите пользователя..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } catch (Exception $e) {
    }
    echo '</br>';

    ?>
    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Принять'), [
            'class' => 'btn btn-success'
        ]) ?>
    </div>
</div>

<script>
    $(document).on("beforeSubmit", "#dynamic-form", function () {
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "name",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modalUser').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>

<?php ActiveForm::end(); ?>
