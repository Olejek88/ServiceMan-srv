<?php
/* @var $model common\models\Operation */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Обновить {modelClass}: ', [
    'modelClass' => 'Операции',
]) . $model->_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Операции'), 'url' => ['index']];
?>
<div class="operation-update box-padding">

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
