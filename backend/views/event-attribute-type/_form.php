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

use common\models\AttributeType;
use common\models\Event;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\EventAttributeType */
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
    $list = Event::find()->orderBy('name')->all();
    $items = ArrayHelper::map($list, 'uuid', 'name');
    unset($list);
    echo $form->field($model, 'eventUuid')->dropDownList($items);
    unset($items);
    ?>

    <?php
    $list = AttributeType::find()->orderBy('name')->all();
    $items = ArrayHelper::map($list, 'uuid', 'name');
    unset($list);
    echo $form->field($model, 'attributeTypeUuid')->dropDownList($items);
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
