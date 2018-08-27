<?php
/* @var $model common\models\UserChannel */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Редактирование пользовательских каналов';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользовательские каналы'), 'url' => ['index']];
?>
<div class="task-stage-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </p>
                    <h6>
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                '_id',
                                'uuid',
                                'messageChannel.title',
                                'messageType.title',
                                'user.name',
                                'channelId',
                                'active',
                                'createdAt',
                                'changedAt',
                            ],
                        ]) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>

</div>
