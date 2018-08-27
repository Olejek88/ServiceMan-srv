<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

use common\models\OperationVerdict;
use common\models\OperationStatus;
use common\models\OperationTemplate;
use common\models\Stage;
use common\models\EquipmentModel;

/* @var $this yii\web\View */
/* @var $model common\models\Operation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operation-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php

    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
    }

    ?>

    <?php

    $stages = Stage::find()->all();
    $items = ArrayHelper::map($stages, 'uuid', 'comment', 'equipment.title');
    $params = [
        'prompt' => 'Выберите операцию..',
    ];

    echo $form->field($model, 'stageUuid')->dropDownList($items, $params);

    ?>

    <?php

    $operationVerdict = OperationVerdict::find()->all();
    $items = ArrayHelper::map($operationVerdict, 'uuid', 'title');

    echo $form->field($model, 'operationVerdictUuid')->dropDownList($items);

    ?>

    <?php

    $operationStatus = OperationStatus::find()->all();
    $items = ArrayHelper::map($operationStatus, 'uuid', 'title');

    echo $form->field($model, 'operationStatusUuid')->dropDownList($items);

    ?>

    <?php

    $operationTemplate = OperationTemplate::find()->all();
    $items = ArrayHelper::map($operationTemplate, 'uuid', 'title');

    echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);

    ?>

    <?= $form->field($model, 'flowOrder')->textInput() ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
