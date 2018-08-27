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
use common\models\TaskTemplate;
use common\models\EquipmentStage;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\TaskEquipmentStage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-equipment-stage-form">

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
    $list = TaskTemplate::find()->all();
    $items = ArrayHelper::map($list, 'uuid', 'title');
    unset($list);
    echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);
    unset($items);
    ?>

    <?php
    $list = EquipmentStage::find()->all();
    $items = [];
    foreach ($list as $item) {
        $t = $item['stageOperation']->stageTemplate->title;
        $t =  $t . ' (' . $item['equipment']->title . ')';
        $items[$item['uuid']] = $t;
    }

    unset($list);
    echo $form->field($model, 'equipmentStageUuid')->dropDownList($items)
        ->label('Этап (Оборудование)');
    unset($items);
    ?>

    <?php echo $form->field($model, 'period')->textInput(['maxlength' => true]) ?>

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
