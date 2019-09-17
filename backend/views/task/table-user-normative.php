<?php
/* @var $searchModel backend\models\EquipmentSearch */

use common\components\MainFunctions;
use common\models\TaskTemplate;
use common\models\Users;
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
        'header' => 'Задача',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['taskTemplate']->title;
        },
        'filter' => ArrayHelper::map(TaskTemplate::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
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
        'header' => 'Элементы',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['equipment']->getFullTitle();
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
                return date("d-m-Y H:i", strtotime($data->startDate));
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
                return date("d-m-Y H:i", strtotime($data->endDate));
            else
                return 'не закрыта';
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
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
                    return 100*($timeSecond - $timeFirst)/($model["taskTemplate"]["normative"]*60);
                return "-";
            }
        }
    ]
];

$users = Users::find()->where(['!=','name','sUser'])->all();
$items = ArrayHelper::map($users, 'uuid', 'name');
$start_date = '2018-12-31';
$end_date = '2021-12-31';
$user='';
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];
if (isset($_GET['user']))
    $user = $_GET['user'];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action="/task/table-user-normative"><table style="padding: 3px; margin: 3px"><tr><td style="width: 300px">' .
            Select2::widget([
                'name' => 'user',
                'language' => 'ru',
                'value' => $user,
                'data' => $items,
                'options' => ['placeholder' => 'Исполнитель'],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]) . '</td><td>&nbsp;</td><td style="width: 300px">' .
            DateTimePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</td><td style="width: 300px">'.
            DateTimePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</td><td style="width: 100px">'.Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-success']).'</td><td style="width: 100px">{export}</td></tr></table></form>',
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
