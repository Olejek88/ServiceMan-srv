<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TaskTemplateEquipment */

$this->title = Yii::t(
    'app',
    'Редактирование связи задачи с этапом'
);
?>
<div class="task-equipment-stage-update box-padding">

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
                            '_form',
                            [
                                'model' => $model,
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
