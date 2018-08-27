<?php

use yii\helpers\Html;

/* @var $model common\models\TaskVerdict */

$this->title = Yii::t('app', 'Создать вердикт задач');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Вердикты задачи'), 'url' => ['index']];
?>
<div class="task-verdict-create box-padding">

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
                    <h6 class='text-center'>
                        * Если вы не нашли <b><?= Html::a('тип задачи', ['/task-type/create'], ['target' => '_blank',]) ?></b>, который вам нужен, создайте его!
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
