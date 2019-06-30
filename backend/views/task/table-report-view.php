<?php
/* @var $searchModel backend\models\TaskSearch
 * @var $titles
 */

use common\components\MainFunctions;
use common\models\Objects;
use common\models\Request;
use common\models\WorkStatus;
use kartik\datecontrol\DateControl;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

if (!isset($titles))
    $titles = "Журнал задач";
$this->title = Yii::t('app', 'ТОИРУС ЖКХ::'.$titles);

$gridColumns = [
    [
        'attribute' => '_id',
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
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'value' => function () {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model) {
            return Yii::$app->controller->renderPartial('task-details', ['model' => $model]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'attribute' => 'taskTemplateUuid',
        'vAlign' => 'middle',
        'header' => 'Задача',
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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'taskDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Дата назначения',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->taskDate)>0)
                return date("d-m-Y H:m", strtotime($data->taskDate));
            else
                return 'не открыт';
        },
        'editableOptions' => [
            'header' => 'Дата назначения',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'dd-MM-yyyy HH:mm',
                'saveFormat' => 'php:Y-m-d H:i:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'deadlineDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Срок',
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'content' => function ($data) {
            if (strtotime($data->deadlineDate)>0)
                return date("d-m-Y H:m", strtotime($data->deadlineDate));
            else
                return 'не задан';
        },
        'editableOptions' => [
            'header' => 'Срок',
            'size' => 'md',
            'inputType' => Editable::INPUT_WIDGET,
            'widgetClass' =>  'kartik\datecontrol\DateControl',
            'options' => [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'dd-MM-yyyy HH:mm',
                'saveFormat' => 'php:Y-m-d H:i:s',
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'workStatusUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'editableOptions'=> function () {
            $status=[];
            $list=[];
            $statuses = WorkStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color='background-color: white';
                if ($stat['uuid']==WorkStatus::CANCELED ||
                    $stat['uuid']==WorkStatus::NEW)
                    $color='background-color: gray';
                if ($stat['uuid']==WorkStatus::IN_WORK)
                    $color='background-color: yellow';
                if ($stat['uuid']==WorkStatus::UN_COMPLETE)
                    $color='background-color: lightred';
                if ($stat['uuid']==WorkStatus::COMPLETE)
                    $color='background-color: green';
                $list[$stat['uuid']] = $stat['title'];
                $status[$stat['uuid']] = "<span class='badge' style='".$color."; height: 12px; margin-top: -3px'> </span>&nbsp;".
                    $stat['title'];
            }
            return [
                'header' => 'Статус задачи',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
        'value' => function ($model) {
            $status =MainFunctions::getColorLabelByStatus($model['workStatus'],'work_status_edit');
            return $status;
        },
        'mergeHeader' => true,
        'format' => 'raw'
    ],
    [
        'vAlign' => 'middle',
        'header' => 'Объект',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['equipment']['object']->getFullTitle();
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Objects::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой']
    ],
    [
        'attribute' => 'taskVerdictUuid',
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'value' => function ($model) {
            $status =MainFunctions::getColorLabelByStatus($model['taskVerdict'],'task_verdict');
            return $status;
        },
        'format' => 'raw'
    ],
/*    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Операции',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $operation_list = "";
            $count = 1;
            $operations = Operation::find()->where(['taskUuid' => $data['uuid']])->all();
            foreach ($operations as $operation) {
                $operation_list = $count.'. '.$operation['operationTemplate']['title'].'</br>';
                $count++;
            }
            return $operation_list;
        }
    ],*/
    [
        'attribute'=>'authorUuid',
        'contentOptions' =>[
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if ($data['authorUuid'])
                return $data['author']->name;
            else
                return 'отсутствует';
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
            if ($cnt>0)
                return $users_list;
            else {
                $name = "<span class='badge' style='background-color: gray; height: 22px'>Не назначены</span>";
                $link = Html::a($name,
                    ['../task/user', 'equipmentUuid' => $data['uuid']],
                    [
                        'title' => 'Исполнители',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalUser'
                    ]);
                return $link;
            }
        },
    ],
    [
        'header' => 'Заявка',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $request = Request::find()->where(['taskUuid' => $data['uuid']])->one();
            if ($request) {
                $name = "<span class='badge' style='background-color: lightblue; height: 22px'>Заявка #".$request['_id']."</span>";
                $link = Html::a($name, ['../request/index', 'uuid' => $request['uuid']], ['title' => 'Заявка']);
                return $link;
            } else
                return "без заявки";
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'comment',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Комментарий'
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Дата начала',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->startDate)>0)
                return date("d-m-Y H:m", strtotime($data->startDate));
            else
                return 'не начата';
        }
    ],
    [
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Дата завершения',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            if (strtotime($data->endDate)>0)
                return date("d-m-Y H:m", strtotime($data->endDate));
            else
                return 'не закончена';
        }
    ]
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px important!'],
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action=""><table style="width: 800px; padding: 3px"><tr><td style="width: 300px">' .
            DateTimePicker::widget([
                'name' => 'start_time',
                'value' => '2018-12-01 00:00:00',
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]).'</td><td style="width: 300px">'.
            DateTimePicker::widget([
                'name' => 'end_time',
                'value' => '2021-12-31 00:00:00',
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
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => true,
    'responsive' => false,
    'hover' => true,
    'floatHeader' => false,
/*    'panelTemplate' =>
        '<div class="panel {type}">
        {panelHeading}
        {panelBefore}
        <img src="/images/1.png">
        {items}
        {panelAfter}
        {panelFooter}
    </div>',*/
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; '.$titles,
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
    'rowOptions' => function($model) {
        if (isset($_GET['uuid'])){
            if ($_GET['uuid'] == $model['uuid'])
                return ['class' => 'danger'];
        }
    }
]);

$this->registerJs('$("#modalUser").on("hidden.bs.modal",
function () {
     window.location.replace("../task/table");
})');

?>
<div class="modal remote fade" id="modalUser">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
