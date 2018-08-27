<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Views
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\StageOperation */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Связь этапов с операциями'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stage-operation-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
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
                                        'label' => 'Шаблон этапа',
                                        'value' => $model->stageTemplate->title
                                    ],
                                    [
                                        'label' => 'Шаблон операции',
                                        'value' => $model->operationTemplate->title
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
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
