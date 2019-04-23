<?php
/* @var $model common\models\WorkStatus */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Статусы операции'), 'url' => ['index']];
?>
<div class="operation-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <?php
                    echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                        ['model' => $model]);
                    ?>
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            '_id',
                            'uuid',
                            'title',
                            'createdAt',
                            'changedAt',
                        ],
                    ]) ?>
                </div>
            </div>

        </div>
    </div>

</div>
