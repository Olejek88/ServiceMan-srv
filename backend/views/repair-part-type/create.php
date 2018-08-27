<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RepairPartType */
/* @var $parentModel yii\base\DynamicModel */


$this->title = Yii::t('app', 'Создать тип запчасти');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы запчастей'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="repair-part-type-create box-padding">

    <div class="box box-default">
        <div class="box-header with-border">
            <h2><?php echo Html::encode($this->title) ?></h2>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="box-body" style="padding: 0 30px 0 30px;">
            <?php
            $form = $this->render(
                '_form',
                [
                    'model' => $model,
                    'parentModel' => $parentModel,
                ]
            );
            echo $form;
            ?>
        </div>
    </div>

</div>
