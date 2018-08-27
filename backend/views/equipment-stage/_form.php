<?php
/**
* PHP Version 7.0
*
* @category Category
* @package  Views
* @author   Дмитрий Логачев <demonwork@yandex.ru>
* @license  http://www.yiiframework.com/license/ License name
* @link     http://www.toirus.ru
*/

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use common\models\Equipment;
use yii\helpers\ArrayHelper;
use common\models\StageOperation;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentStage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-stage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')
            ->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')
            ->textInput(
                ['maxlength' => true, 'value' => (new MainFunctions())->GUID()]
            );
    }
    ?>

    <?php
    $list = Equipment::find()->all();
    $items = ArrayHelper::map($list, 'uuid', 'title');
    unset($list);
    echo $form->field($model, 'equipmentUuid')->dropDownList($items);
    unset($items);

    ?>

    <?php
    $stages = StageOperation::find()->all();
    $items = ArrayHelper::map($stages, 'uuid', 'stageTemplate.title');
    unset($stages);
    echo $form->field($model, 'stageOperationUuid')->dropDownList($items);
    unset($items);

    ?>

    <?php
    $stages = StageOperation::find()->all();
    $items = ArrayHelper::map($stages, 'uuid', 'operationTemplate.title');
    unset($stages);
    echo $form->field($model, 'stageOperationUuid')->dropDownList($items);
    unset($items);

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
