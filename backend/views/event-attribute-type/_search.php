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

/* @var $this yii\web\View */
/* @var $model backend\models\EventAttributeTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-equipment-stage-search box-padding">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?php echo $form->field($model, '_id') ?>

    <?php echo $form->field($model, 'uuid') ?>

    <?php echo $form->field($model, 'eventUuid') ?>

    <?php echo $form->field($model, 'attributeTypeUuid') ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('app', 'Search'), ['class' => 'btn btn-primary']
        ) ?>
        <?php echo Html::resetButton(
            Yii::t('app', 'Reset'), ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
