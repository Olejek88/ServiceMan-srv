<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OperationRepairPart */

$this->title = Yii::t('app', 'Добавить запчасть для операции');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Запчасти для операций'),
    'url' => ['index']
];
?>
<div class="operation-repair-part-create box-padding">
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
                                'шаблон операции',
                                ['/operation-template/create'],
                                ['target' => '_blank',]
                            ) ?></b>
                        или <b><?php
                            echo Html::a(
                                'запчасть',
                                ['/repair-part/create'],
                                ['target' => '_blank',]
                            ) ?></b>,
                        создайте!
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
