<?php
/* @var $task */

use common\components\MainFunctions;
use common\models\Request;
use kartik\detail\DetailView;
use yii\helpers\Html;

$title = 'Задача №' . $task['_id'];
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?= $title ?></h4>
</div>
<div class="modal-body">
    <?php
    $users = $task['users'];
    $users_list = "";
    $cnt = 0;
    foreach ($users as $user) {
        if ($cnt > 0) $users_list .= ', ';
        $users_list .= $user['name'];
        $cnt++;
    }
    echo DetailView::widget(
        [
            'model' => $task,
            'mode'=>DetailView::MODE_VIEW,
            'attributes' => [
                [
                    'label' => '_id',
                    'value' => $task->_id
                ],
                [
                    'label' => 'Шаблон этапа',
                    'value' => $task->taskTemplate->title
                ],
                [
                    'label' => 'Дата назначения',
                    'attribute' => 'taskDate',
                    'value' => date("d-m-Y H:m", strtotime($task->taskDate))
                ],
                [
                    'label' => 'Срок',
                    'value' => date("d-m-Y H:m", strtotime($task->deadlineDate))
                ],
                [
                    'label' => 'Статус',
                    'format'=>'raw',
                    'value' => MainFunctions::getColorLabelByStatus($task['workStatus'], 'work_status_edit')
                ],
                [
                    'label' => 'Объект',
                    'value' => $task['equipment']['object']->getFullTitle()
                ],
                [
                    'label' => 'Вердикт',
                    'format'=>'raw',
                    'value' => MainFunctions::getColorLabelByStatus($task['taskVerdict'], 'task_verdict')
                ],
                [
                    'label' => 'Автор',
                    'value' => $task['authorUuid']
                ],
                [
                    'label' => 'Исполнители',
                    'value' => $users_list
                ],
                [
                    'label' => 'Заявка',
                    'format'=>'raw',
                    'value' => $task['request']
                ],

                [
                    'label' => 'Комментарий',
                    'value' => $task->comment
                ],
                [
                    'label' => 'Дата начала',
                    'value' => date("d-m-Y H:m", strtotime($task->startDate))
                ],
                [
                    'label' => 'Дата завершения',
                    'value' => date("d-m-Y H:m", strtotime($task->endDate))
                ]
            ],
        ]
    ) ?>
</div>
