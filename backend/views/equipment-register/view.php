<?php

use yii\widgets\DetailView;

/* @var $model \common\models\EquipmentRegister */

$this->title = 'Запись в журнале событий';
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Журнал оборудования'), 'url' => ['index']
];
?>
<div class="order-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                Запись в журнале событий
            </h3>
        </div>
        <div class="panel-body">
            <h1 class="text-center"></h1>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    'uuid',
                                    [
                                        'label' => 'Тип записи',
                                        'value' => $model['registerType']->title
                                    ],
                                    [
                                        'label' => 'Пользователь',
                                        'value' => $model['user']->name
                                    ],
                                    'date',
                                    [
                                        'label' => 'Оборудование',
                                        'value' => $model['equipment']->title
                                    ],
                                    'fromParameterUuid',
                                    'toParameterUuid',
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
