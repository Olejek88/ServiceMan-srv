<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentAttribute */

$this->title = Yii::t('app', 'Создать аттрибут для оборудования');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Аттрибуты'),
    'url' => ['index']
];
?>
<div class="equipment-model-create box-padding">

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
                    <h6 class='text-center'>
                        * Если вы не нашли
                        <b><?php
                            echo Html::a(
                                'тип аттрибута',
                                ['/attribute-type/create'],
                                ['target' => '_blank',]
                            ) ?></b>,
                        создайте его!
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
