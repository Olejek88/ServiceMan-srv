<?php
/* @var $model \common\models\Objects */

/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\view
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var \common\models\Objects $model */

$this->title = $model->title;
?>
<div class="order-verdict-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <img class="user-image-photo" src="<?php echo Html::encode($model->getPhotoUrl()) ?>" alt="">
                <?php echo Html::encode($this->title) ?>
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
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'label' => '_id',
                                        'value' => $model->_id
                                    ],
                                    [
                                        'label' => 'Uuid',
                                        'value' => $model->uuid
                                    ],
                                    [
                                        'label' => 'Тип объекта',
                                        'value' => $model['objectType']['title']
                                    ],
                                    [
                                        'label' => 'Имя',
                                        'value' => $model->title
                                    ],
                                    [
                                        'label' => 'Описание',
                                        'value' => $model->description
                                    ],
                                    [
                                        'label' => 'Широта',
                                        'value' => $model->latitude
                                    ],
                                    [
                                        'label' => 'Долгота',
                                        'value' => $model->longitude
                                    ],
                                    [
                                        'label' => 'Создан',
                                        'value' => $model->createdAt
                                    ],
                                    [
                                        'label' => 'Изменен',
                                        'value' => $model->changedAt
                                    ],
                                ],
                            ]
                        )
                        ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
