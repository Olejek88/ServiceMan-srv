<?php

use common\components\MainFunctions;
use common\models\MeasureType;
use common\models\Operation;
use common\models\Users;
use common\models\WorkStatus;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\select2\Select2;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Таблица задач');

$gridColumns = [
    [
        'attribute' => '_id',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['taskTemplate']->title;
        }
    ],
    [
        'header' => 'Исполнитель',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $users = $data['users'];
            $users_list="";
            $cnt=0;
            foreach ($users as $user) {
                if ($cnt>0) $users_list .= ',';
                $users_list .= $user['name'];
                $cnt++;
            }
            return $users_list;
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Оборудование',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['equipment']['title'];
        }
    ],
    [
        'attribute' => 'workStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function ($model) {
            $status =MainFunctions::getColorLabelByStatus($model['workStatus'],'work_status');
            return $status;
        },
        'mergeHeader' => true,
        'format' => 'raw'
    ],
    [
        'attribute' => 'startDate',
        'header' => 'Начало',
        'hAlign' => 'center',
        'mergeHeader' => true,
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->startDate))
                return date("Y-m-d h:m", strtotime($data->startDate));
            else
                return 'не начата';
        }
    ],
    [
        'attribute' => 'endDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Закончена',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->endDate))
                return date("Y-m-d h:m", strtotime($data->endDate));
            else
                return 'не закрыта';
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Норматив',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data["taskTemplate"]["normative"];
        }
    ],
    [
        'headerOptions' => ['class' => 'text-center'],
        'header' => 'Время',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'value' => function ($model) {
            if ($model["startDate"] && $model["endDate"]) {
                $timeFirst = strtotime($model["startDate"]);
                $timeSecond = strtotime($model["endDate"]);
                return $timeSecond - $timeFirst;
            }
            return "-";
        },
        'format' => 'raw'
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => '%',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($model) {
            if ($model["startDate"] && $model["endDate"]) {
                $timeFirst = strtotime($model["startDate"]);
                $timeSecond = strtotime($model["endDate"]);
                if ($model["taskTemplate"]["normative"])
                    return 100*($timeSecond - $timeFirst)/$model["taskTemplate"]["normative"];
                return "-";
            }
        }
    ]
];

$users = Users::find()->where(['!=','name','sUser'])->all();
$items = ArrayHelper::map($users, 'uuid', 'name');

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action="/task/table-user"><table style="width: 100%; padding: 3px"><tr><td>' .
            Select2::widget([
                'name' => 'user',
                'language' => 'ru',
                'data' => $items,
                'options' => ['placeholder' => 'Исполнитель'],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]) . '</td><td>&nbsp;</td><td>'.Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']).'</td><td style="text-align: right">{export}</td></tr></table></form>',
            'options' => ['style' => 'width:100%']
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'tasks'
    ],
    'pjax' => true,
    'options' => ['style' => 'width:100%'],
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => true,
    'responsive' => false,
    'hover' => true,
    'floatHeader' => false,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Выполненные задачи',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
