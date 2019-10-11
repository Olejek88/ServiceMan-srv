<?php
/* @var $searchModel backend\models\UsersSearch */

use common\models\User;
use common\models\Users;
use kartik\editable\Editable;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Управление пользователями');

$editableOptions = function ($model) {
    $options = [
        'inputType' => kartik\editable\Editable::INPUT_CHECKBOX,
        'options' => [
            'label' => 'Активен ',
        ],
    ];
    return $options;
};

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'name',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '180px',
        'header' => 'Роль пользователя',
        'mergeHeader' => true,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $widget) {
            $assignments = Yii::$app->getAuthManager()->getAssignments($model['user_id']);
            foreach ($assignments as $value) {
                if ($value->roleName==User::ROLE_ADMIN)
                    return '<span class="label label-danger">Администратор</span>';
                if ($value->roleName==User::ROLE_OPERATOR)
                    return '<span class="label label-success">Оператор</span>';
                if ($value->roleName==User::ROLE_DISPATCH)
                    return '<span class="label label-info">Диспетчер</span>';
                if ($value->roleName==User::ROLE_DIRECTOR)
                    return '<span class="label label-info">Директор</span>';
            }
            return '';
        },
        'editableOptions'=> function ($model, $key, $index, $widget) {
            return [
                'header' => 'Тип',
                'id' => 'type',
                'name' => 'type',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => [
                    User::ROLE_ADMIN =>'Администратор',
                    User::ROLE_OPERATOR =>'Оператор',
                    User::ROLE_DISPATCH =>'Диспетчер',
                    User::ROLE_DIRECTOR =>'Директор',
                ]
            ];
        },
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '180px',
        'header' => 'Тип пользователя',
        'mergeHeader' => true,
        'format' => 'raw',
        'value' => function ($model) {
            if ($model['type'] == Users::USERS_ARM)
                return '<span class="label label-info">Оператор</span>';
            if ($model['type'] == Users::USERS_WORKER)
                return '<span class="label label-info">Исполнитель</span>';
            if ($model['type'] == Users::USERS_ARM_WORKER)
                return '<span class="label label-info">Оператор</span>&nbsp;<span class="label label-info">Исполнитель</span>';
            return '';
        }
    ],
    [
        'attribute' => 'whoIs',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Должность',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Изменение',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'active',
        'mergeHeader' => true,
        'format' => 'html',
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            if ($model->active==1)
                return GridView::ICON_ACTIVE;
            else
                return GridView::ICON_INACTIVE;
        },
        'editableOptions' => $editableOptions
    ],
/*    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'active',
        'header' => 'Статус',
        'editableOptions'=> [
            'asPopover' => false,
        ],
        'value' => function ($model, $key, $index, $widget) {
            if ($model->active==1) return \yii\helpers\Html::'<span class="fas fa-check text-success"></span>';
            else return '<span class="fas fa-times text-danger"></span>';
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'template' => '{view} {update}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'users-table',
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
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'users'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Пользователи',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);
