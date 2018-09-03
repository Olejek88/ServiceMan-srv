<?php

use yii\helpers\Html;

/* @var $model \common\models\Equipment */

$this->title = Yii::t('app', 'Создать оборудование');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Оборудование'),
    'url' => ['index']
];
?>
<div class="equipment-create box-padding">

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
                                'модель оборудования',
                                ['/equipment-type/create'],
                                ['target' => '_blank',]
                            ) ?></b>,
                        <b><?php
                            echo Html::a(
                                'статус оборудования',
                                ['/equipment-status/create'],
                                ['target' => '_blank',]
                            ) ?></b>, создайте их.
                    </h6>
                </div>
            </div>

        </div>
    </div>

</div>
