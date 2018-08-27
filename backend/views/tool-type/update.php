<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ToolType */
/* @var $parentModel \yii\base\DynamicModel */

$title = Yii::t(
    'app',
    'Обновить {modelClass}: ',
    ['modelClass' => 'Типы инструментов',]
);
$this->title = $title . $model->title;


$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Tool Types'),
    'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->title,
    'url' => ['view', 'id' => $model->_id]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Обновить');
?>
<div class="tool-type-update box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?php echo $this->render(
                            '_form',
                            [
                                'parentModel' => $parentModel,
                                'model' => $model,
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
