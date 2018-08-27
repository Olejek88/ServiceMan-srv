<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RepairPartType */
/* @var $parentModel \yii\base\DynamicModel */


$this->title = Yii::t(
    'app',
    'Обновить {modelClass}: ',
    [
        'modelClass' => 'Типы запчастей',
    ]
);
$this->title .= $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы запчастей'), 'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $model->title, 'url' => ['view', 'id' => $model->_id]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Обновить');
?>
<div class="repair-part-type-update box-padding">

    <div class="box box-default">
        <div class="box-header with-border">
            <h2><?php echo Html::encode($this->title) ?></h2>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="box-body" style="padding: 30px;">
            <?php echo $this->render(
                '_form',
                [
                    'model' => $model,
                    'parentModel' => $parentModel,
                ]
            ) ?>
        </div>
    </div>

</div>
