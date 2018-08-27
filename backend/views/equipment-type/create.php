<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OperationType */
/* @var $parentModel yii\base\DynamicModel */

$this->title = Yii::t('app', 'Создать тип оборудования');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Типы оборудования'),
    'url' => ['index']
];
?>
<div class="equipment-type-create box-padding">

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
                                'model' => $model,
                                'parentModel' => $parentModel,
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
