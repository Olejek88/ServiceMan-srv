<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var \common\models\Documentation $model */
/* @var array $entity */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Документация'),
    'url' => ['index']
];
?>
<div class="order-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            <div class="">
                <a href="<?php echo Html::encode($model->getDocUrl()) ?>">Документ</a>
            </div>

        </div>
        <div class="panel-body">
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php echo Html::a(Yii::t('app', 'Обновить'), ['update', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
                        <?php echo Html::a(
                            Yii::t('app', 'Удалить'),
                            ['delete', 'id' => $model->_id],
                            [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Вы действительно хотите удалить данный элемент?'),
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
                                    '_id',
                                    'uuid',
                                    [
                                        'label' => $entity['label'],
                                        'value' => $entity['title']
                                    ],
                                    [
                                        'label' => 'Тип документации',
                                        'value' => $model->documentationType['title']
                                    ],
                                    'title',
                                    'createdAt',
                                    'changedAt',
                                    'path:ntext',
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
