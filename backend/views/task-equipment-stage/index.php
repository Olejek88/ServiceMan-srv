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
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TaskEquipmentStageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Связь задачи с этапом');
?>
<div class="task-equipment-stage-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="panel-body">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Создать связь задачи с этапом'),
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
                                            'style' => 'width: 50px; text-align: center'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'content' => function ($data) {
                                            return $data->_id;
                                        }
                                    ],
                                    [
                                        //'attribute' => 'equipment',
                                        'label' => 'Задача',
                                        'contentOptions' => [
                                            'class' => 'table_class'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'taskTemplate.title'
                                    ],
                                    [
                                        'label' => 'Этап (Оборудование)',
                                        //'attribute' => 'stageOperation',
                                        'contentOptions' => [
                                            'class' => 'table_class'
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        //'value' => 'equipmentStage.equipment.title',
                                        'content' => function ($data) {
                                            $s = $data->equipmentStage->stageOperation->stageTemplate->title;
                                            $e = $data->equipmentStage->equipment->title;
                                            $result = $s . ' (' . $e . ')';
                                            return $result;
                                        }
                                    ],
                                    [
                                        'label' => 'Периодичность',
                                        //'attribute' => 'stageOperation',
                                        'contentOptions' => [
                                            'class' => 'table_class',
                                        ],
                                        'headerOptions' => [
                                            'class' => 'text-center'
                                        ],
                                        'value' => 'period'
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'headerOptions' => [
                                            'class' => 'text-center',
                                            'width' => '70'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'text-center',
                                        ],
                                        'template' => '{view} {update} {delete}{link}',
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
