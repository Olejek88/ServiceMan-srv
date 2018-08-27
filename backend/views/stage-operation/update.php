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

/* @var $this yii\web\View */
/* @var $model common\models\StageOperation */

$this->title = Yii::t('app', 'Связь этапа с операцией');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Связь этапов с операциями'), 'url' => ['index']
];
?>
<div class="stage-operation-update box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="box-tools pull-right">
            <span class="label label-default"></span>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?php echo $this->render(
                            '_form', [
                                'model' => $model,
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
