<?php

use yii\helpers\Html;

/* @var $model common\models\TaskVerdict */

$this->title = Yii::t('app', 'Обновить вердикт задачи');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Вердикты задачи'), 'url' => ['index']];
?>
<div class="task-verdict-update box-padding">

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
