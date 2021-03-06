<?php

use yii\helpers\Html;

/* @var $model common\models\UserHouse */

$this->title .= 'Пользователи и дома';
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Пользователи и дома'),
    'url' => ['index']
];
?>
<div class="equipment-model-update box-padding">

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
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
