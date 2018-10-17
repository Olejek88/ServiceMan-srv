<?php
/*  @var $model \common\models\PhotoFlat */

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="order-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">Фотография квартиры</h3>
        </div>
        <div class="panel-body">
            <?php
            $path = '/storage/flat/' . $model->uuid . '.jpg';
            ?>
            <div class="user-image-photo">
                <img src="<?php echo Html::encode($path) ?>" alt="">
            </div>

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?= Html::a(Yii::t('app', 'Обновить'), ['update', 'id' => $model->_id],
                            ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->_id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app', 'Вы действительно хотите удалить данный элемент?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    </p>
                    <h6>
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
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
