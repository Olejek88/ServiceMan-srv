<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Orders */

$this->title = Yii::t('app', 'Создать наряд');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Наряды'), 'url' => ['index']];
?>
<div class="orders-create box-padding">

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
<div class="modal remote fade" id="modalvote">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
