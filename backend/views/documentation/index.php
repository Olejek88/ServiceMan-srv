<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\components\MyHelpers;

/* @var $searchModel */
/* @var $dataProvider */

$this->title = Yii::t('app', 'Документация и справочники');
?>
<div class="equipment-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 450px; margin: 0 auto;">
                <li class="active"><a href="/documentation">Документация</a></li>
                <li class=""><a href="/documentation-type">Тип</a></li>
                <li class=""><a href="/equipment">Оборудование</a></li>
                <li class=""><a href="/equipment-type">Типы оборудования</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Создать'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>

                    <h6 class="text-center">
                        <?php echo GridView::widget(
                            [
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'tableOptions' => [
                                    'class' => 'table-striped table table-bordered table-hover table-condensed'
                                ],
                                'columns' => [
                                    [
                                        'attribute' => '_id',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                            'style' => 'width: 50px; text-align: center;'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'content' => function ($data) {
                                            return $data->_id;
                                        }
                                    ],
                                    [
                                        'attribute' => 'title',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'content' => function ($data) {
                                            $tmpPath = '/'
                                                . $data->equipmentUuid
                                                . "/" . $data->path;
                                            return Html::a(
                                                Html::encode($data->title),
                                                Url::to(MyHelpers::getImgUrlPath($tmpPath)),
                                                ['target' => '_blank']
                                            );
                                        }
                                    ],
                                    [
                                        'attribute' => 'equipment',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'equipment.title'
                                    ],
                                    [
                                        'attribute' => 'equipmentModel',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'equipmentModel.title'
                                    ],
                                    [
                                        'attribute' => 'documentationTypeUuid',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'documentationType.title'
                                    ],
                                    [
                                        'attribute' => 'required',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'required'
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'headerOptions' => [
                                            'class' => 'text-center',
                                            'width' => '70'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'template' => '{view} {update} {delete}',
                                    ],
                                ],
                            ]
                        ); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
