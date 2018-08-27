<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\RepairPartType;
use common\models\RepairPartTypeTree;
use common\components\TypeTreeHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RepairPartSearchType */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Типы запчастей');
?>
<div class="repair-part-type-index box-padding-index">

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="text-center" style="color: #333;"><?php echo Html::encode($this->title) ?></h3>
            <ul class="nav nav-tabs" style="width: 203px; margin: 0 auto;">
                <li class=""><a href="/repair-part">Список</a></li>
                <li class="active"><a href="/repair-part-type">Тип</a></li>
            </ul>
        </div>
        <div class="box-body" style="padding: 0 10px 0 10px;">
            <p>
                <?php
                echo Html::a(
                    Yii::t('app', 'Новый тип'),
                    ['create'],
                    ['class' => 'btn btn-success']
                );
                ?>
            </p>
            <div class="box-body-table">
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
                                'headerOptions' => ['class' => 'text-center'],
                                'content' => function ($data) {
                                    return $data->_id;
                                }
                            ],
                            [
                                'header' => 'Родитель',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                    'style' => 'padding: 5px 10px 5px 10px;'
                                ],
                                'headerOptions' => [
                                    'class' => 'text-center'
                                ],
                                'content' => function ($data) {
                                    $parentId = TypeTreeHelper::getParentId(
                                        $data->_id,
                                        RepairPartType::class,
                                        RepairPartTypeTree::class
                                    );
                                    $parentType = RepairPartType::findOne($parentId);
                                    if ($parentType) {
                                        $parentType = $parentType->title;
                                    } else {
                                        $parentType = 'Корень';
                                    }
                                    return $parentType;
                                }
                            ],
                            [
                                'attribute' => 'title',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'content' => function ($data) {
                                    return $data->title;
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Действия',
                                'headerOptions' => [
                                    'class' => 'text-center', 'width' => '70'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-center',
                                ],
                                'template' => '{view} {update} {delete}{link}',
                            ],
                        ],
                    ]
                );
                ?>
            </div>
        </div>
    </div>
</div>
