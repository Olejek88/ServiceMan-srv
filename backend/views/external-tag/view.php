<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/*  @var $model \common\models\ExternalTag
 * @var $system
 * @var $actionType
 */

$this->title = 'Редактируем тег '.$model->tag;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Теги внешних систем'), 'url' => ['index']
];
?>
<div class="order-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">
            <h1 class="text-center"></h1>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Обновить'),
                            ['update', 'id' => $model->_id],
                            ['class' => 'btn btn-primary']
                        ) ?>
                        <?php echo Html::a(
                            Yii::t('app', 'Удалить'),
                            ['delete', 'id' => $model->_id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t(
                                        'app',
                                        'Вы действительно хотите удалить данный элемент?'
                                    ),
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>
                    </p>
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    'uuid',
                                    'externalSystem.title',
                                    'tag',
                                    'value',
                                    'equation',
                                    'target',
                                    'equipment.title',
                                    'actionType.title',
                                    'taskEquipmentStage.taskTemplate.title',
                                    'createdAt',
                                    'changedAt',
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
