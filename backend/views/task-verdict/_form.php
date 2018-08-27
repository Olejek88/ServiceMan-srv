<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\commands\MainFunctions;
use common\models\TaskType;

/* @var $this yii\web\View */
/* @var $model common\models\TaskVerdict */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-verdict-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
        }

    ?>


    <?php

        $tasktype = TaskType::find()->all();
        $items    = ArrayHelper::map($tasktype,'uuid','title');

        echo $form->field($model, 'taskTypeUuid')->dropDownList($items);

    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
