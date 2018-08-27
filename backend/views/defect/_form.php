<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\datetimepicker\DateTimePicker;
use app\commands\MainFunctions;
use common\models\DefectType;
use common\models\Task;
use common\models\Users;
use common\models\Equipment;

/* @var $this yii\web\View */
/* @var $model common\models\Tool */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
        }

    ?>

    <?php

        $defectType = DefectType::find()->all();
        $items    = ArrayHelper::map($defectType, 'uuid','title');

        echo $form->field($model, 'defectTypeUuid')->dropDownList($items);
    ?>

    <?php
        $equipment = Equipment::find()->all();
        $items     = ArrayHelper::map($equipment, 'uuid', 'title', 'inventoryNumber');
        $params    = [
            'prompt' => 'Выберите оборудование..',
        ];

        echo $form->field($model, 'equipmentUuid')->dropDownList($items, $params);
    ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 4, 'style' => 'resize: none;']) ?>
    
    <?= $form->field($model, 'process')
        ->checkbox([
        'label' => 'Обработан',
        'labelOptions' => [
            'style' => 'padding-left:20px;'
        ]
    ]);
    ?>
    
    <?php
        $task  = Task::find()->all();
        $items = ArrayHelper::map($task,'uuid','comment');
        echo $form->field($model, 'taskUuid')->dropDownList($items);
    ?>

    <?php
        $user  = Users::find()->all();
        $items = ArrayHelper::map($user, 'uuid','name');
        echo $form->field($model, 'userUuid')->dropDownList($items);

    ?>


    <div class="pole-mg" style="margin: 20px 20px 20px 15px;">
    <p style="width: 0; margin-bottom: 0;">Дата</p>
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

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <h6 class='text-center'>
        * Если вы не нашли <b><a href="/defect-type">тип дефекта</a></b>, который вам нужен, создайте его!
    </h6>

    <?php ActiveForm::end(); ?>

</div>
