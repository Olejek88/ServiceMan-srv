<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\StageType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\StageType */
/* @var $form yii\widgets\ActiveForm */
/* @var $parentModel yii\base\DynamicModel */
?>

<div class="stage-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    // uuid
    $uuidField = $form->field($model, 'uuid');
    $uuidFieldValue = null;
    $allTypes = array(
        '00000000-0000-0000-0000-000000000000' => 'Корень',
    );

    if (!$model->isNewRecord) {
        $types = StageType::find()->where(['!=', '_id', $model->_id])->all();
        $uuidFieldValue = $uuidField->textInput(
            [
                'maxlength' => true,
                'readonly' => true
            ]
        );
    } else {
        $types = StageType::find()->all();
        $uuidFieldValue = $uuidField->textInput(
            [
                'maxlength' => true,
                'value' => (new MainFunctions)->GUID()
            ]
        );
    }

    echo $uuidFieldValue;
    ?>

    <?php
    $allTypes += ArrayHelper::map($types, 'uuid', 'title');
    unset($types);
    // список предков создаваемого/редактируемого типа
    echo $form->field($parentModel, 'parentUuid')->dropDownList($allTypes);
    unset($allTypes);
    ?>

    <?php
    echo $form->field($model, 'title')->textInput(['maxlength' => true])
    ?>

    <div class="form-group text-center">

        <?php
        if ($model->isNewRecord) {
            $buttonText = Yii::t('app', 'Создать');
            $buttonClass = 'btn btn-success';
        } else {
            $buttonText = Yii::t('app', 'Обновить');
            $buttonClass = 'btn btn-primary';
        }

        echo Html::submitButton($buttonText, ['class' => $buttonClass])
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
