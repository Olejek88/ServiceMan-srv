<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MeasuredValue */

$this->title = Yii::t('app', 'Добавить измеренное значение');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Измеренные значения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="measured-value-create box-padding">

    <div class="box box-default">
        <div class="box-header with-border">
            <h2><?= Html::encode($this->title) ?></h2>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="box-body" style="padding: 0 30px 0 30px;">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
