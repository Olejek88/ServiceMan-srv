<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\commands\MainFunctions;
use common\models\ToolType;

/* @var $this yii\web\View */
/* @var $model common\models\Tool */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')
            ->textInput(
                ['maxlength' => true, 'value' => (new MainFunctions)->GUID()]
            );
    }
    ?>

    <?php

    $types = ToolType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    echo $form->field($model, 'toolTypeUuid')->dropDownList($items);
    unset($items);
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-center">
        <?php
        if ($model->isNewRecord) {
            $msg = Yii::t('app', 'Создать');
            $class = 'btn btn-success';
        } else {
            $msg = Yii::t('app', 'Обновить');
            $class = 'btn btn-primary';
        }

        echo Html::submitButton($msg, ['class' => $class]);
        ?>
    </div>

    <h6 class='text-center'>
        * Если вы не нашли
        <b><a href="/tool-type">тип инструмента</a></b>,
        который вам нужен, создайте его!
    </h6>

    <?php ActiveForm::end(); ?>

</div>
