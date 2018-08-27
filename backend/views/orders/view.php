<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Orders */
/* @var $level common\models\OrderLevel */
/* @var $author common\models\Users */
/* @var $status common\models\OrderStatus */
/* @var $verdict common\models\OrderVerdict */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Наряды'), 'url' => ['index']];
?>
<div class="order-status-view box-padding">

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
                        <?= Html::a(Yii::t('app', 'Список'), ['table', 'id' => $model->_id], ['class' => 'btn btn-info']) ?>
                        <?= Html::a(Yii::t('app', 'Обновить'), ['update', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->_id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app', 'Вы действительно хотите удалить данный элемент?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                        <?= Html::a(Yii::t('app', 'Наряд'), ['order', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Yii::t('app', 'Отчет'), ['report', 'id' => $model->_id], ['class' => 'btn btn-info']) ?>
                    </p>
                    <h6>
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                '_id',
                                'uuid',
                                [
                                    'label' => 'Уровень',
                                    'value' => $level['title']
                                ],
                                'title',
                                'comment',
                                'reason',
                                [
                                    'label' => 'Автор',
                                    'value' => $author['name']
                                ],
                                [
                                    'label' => 'Исполнитель',
                                    'value' => $user['name']
                                ],
                                'receivDate',
                                'startDate',
                                'openDate',
                                'closeDate',
                                [
                                    'label' => 'Статус',
                                    'value' => $status['title']
                                ],
                                [
                                    'label' => 'Вердикт',
                                    'value' => $verdict['title']
                                ],
                                'attemptSendDate',
                                'attemptCount',
                                'updated',
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
