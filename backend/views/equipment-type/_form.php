<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\EquipmentType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentType */
/* @var $form yii\widgets\ActiveForm */
/* @var $parentModel yii\base\DynamicModel */
?>

<div class="equipment-type-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-documentation',
            'options' => [
                'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                'enctype' => 'multipart/form-data'
            ],
        ]
    );
    ?>

    <?php
    // uuid
    $uuidField = $form->field($model, 'uuid');
    $uuidFieldValue = null;
    $allTypes = array(
        '00000000-0000-0000-0000-000000000000' => 'Корень',
    );

    if (!$model->isNewRecord) {
        $types = EquipmentType::find()->where(['!=', '_id', $model->_id])->orderBy('title')->all();
        $uuidFieldValue = $uuidField->textInput(
            [
                'maxlength' => true,
                'readonly' => true
            ]
        );
    } else {
        $types = EquipmentType::find()->orderBy('title')->all();
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
    //echo $form->field($parentModel, 'parentUuid')->dropDownList($allTypes);
    echo $form->field($parentModel, 'parentUuid')->widget(Select2::classname(),
        [
            'data' => $allTypes,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    unset($allTypes);
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

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
