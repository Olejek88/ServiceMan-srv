<?php
/* @var $model common\models\TaskVerdict */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="order-status-view box-padding" style="width: 95%; min-height: 782px">
    <?php
    echo $this->render('@backend/views/yii2-app/layouts/references-menu.php');
    ?>
    <div class="panel panel-default" style="float: right; width: 75%">
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
