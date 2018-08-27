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
use common\models\OperationTemplate;
use yii\helpers\ArrayHelper;
use common\models\StageTemplate;
use app\commands\MainFunctions;

/* @var $this yii\web\View */
/* @var $model common\models\StageOperation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stage-operation-form">

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
    $stages = StageTemplate::find()->all();
    $items = ArrayHelper::map($stages, 'uuid', 'title');
    unset($stages);
    echo $form->field($model, 'stageTemplateUuid')->dropDownList($items);
    unset($items);

    ?>

    <?php
    $templates = OperationTemplate::find()->all();
    $items = ArrayHelper::map($templates, 'uuid', 'title');
    unset($templates);
    echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);
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
