<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MessageChannel */

$this->title = Yii::t('app', 'Обновить {modelClass}: ', [
    'modelClass' => 'Message Channel',
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Message Channel'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Обновить');
?>
<div class="tool-type-update box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
