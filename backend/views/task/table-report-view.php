<?php
/* @var $searchModel backend\models\TaskSearch */

use common\components\MainFunctions;
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

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Журнал осмотров');

$gridColumns = [
    [
        'attribute' => '_id',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'attribute' => 'startDate',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'header' => 'Дата осмотра',
        'contentOptions' => ['class' => 'kv-sticky-column'],
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
        }
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
    [
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
            '<form action="/task/table-report-view"><table style="width: 800px; padding: 3px"><tr><td style="width: 300px">' .
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
        'heading' => '<i class="glyphicon glyphicon-user"></i>&nbsp; Журнал осмотров',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
