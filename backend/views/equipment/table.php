<?php
/* @var $searchModel backend\models\EquipmentSearch */

use common\models\Measure;
use common\models\Message;
use common\models\PhotoEquipment;
use common\models\Subject;
use common\models\UserHouse;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Таблица для анализа');

$gridColumns = [
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => function ($data) {
            return $data['flat']['house']['street']['title'];
        },
        'header' => 'Улица',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => 'Дом',
        'value' => function ($data) {
            return $data['flat']['house']['number'];
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => 'Пом.',
        'value' => function ($data) {
            return $data['flat']['number'];
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => 'Субъект',
        'value' => function ($data) {
            $subject = Subject::getSubjectName($data['flatUuid']);
            if ($subject != null)
                return substr($subject, 0, 80);
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => 'Измерение #1',
        'value' => function ($data) {
            $measure = Measure::getLastMeasureBetweenDates($data['uuid'], '2018-09-27 00:00:00',
                '2018-10-12 00:00:00');
            if ($measure != null)
                return $measure['value'];
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '130px',
        'header' => 'Дата #1',
        'value' => function ($data) {
            $measure = Measure::getLastMeasureBetweenDates($data['uuid'], '2018-09-27 00:00:00',
                '2018-10-12 00:00:00');
            if ($measure != null)
                return $measure['date'];
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => 'Измерение #2',
        'value' => function ($data) {
            $measure = Measure::getLastMeasureBetweenDates($data['uuid'], '2018-10-12 00:00:00',
                '2018-10-20 00:00:00');
            if ($measure != null)
                return $measure['value'];
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '130px',
        'header' => 'Дата #2',
        'value' => function ($data) {
            $measure = Measure::getLastMeasureBetweenDates($data['uuid'], '2018-10-12 00:00:00',
                '2018-10-20 00:00:00');
            if ($measure != null)
                return $measure['date'];
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '200px',
        'header' => 'Сообщение',
        'value' => function ($data) {
            $message = Message::getLastMessage($data['flatUuid']);
            if ($message != null)
                return $message;
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '140px',
        'header' => 'Пользователь',
        'value' => function ($data) {
            $user = UserHouse::getUserName($data['flat']['houseUuid']);
            if ($user != null)
                return $user;
            else
                return '';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => '№Пос.',
        'value' => function ($data) {
            $message_flat_count = Message::find()
                ->where(['flatUuid' => $data['flat']['uuid']])
                ->count();
            $photo_flat_count = PhotoEquipment::find()
                ->where(['equipmentUuid' => $data['uuid']])
                ->count();
            return '[' . $photo_flat_count . '/' . $message_flat_count . ']';
        },
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '80px',
        'header' => '№Изм.',
        'value' => function ($data) {
            $measure_count = Measure::find()
                ->where(['equipmentUuid' => $data['uuid']])
                ->count();
            return $measure_count;
        },
    ],
];

echo GridView::widget([
    'id' => 'equipment-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'equipments'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Оборудование для анализа',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
