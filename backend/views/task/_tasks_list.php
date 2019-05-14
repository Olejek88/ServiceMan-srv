<?php
/* @var $stages */

use common\models\WorkStatus;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">История работ</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Исполнитель</th>
            <th>Задача</th>
            <th>Статус</th>
            <th>Дата</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= "?" ?></td>
                <td><?= $task['taskTemplate']['title'] ?></td>
                <td><?php
                    $class = "critical2";
                    if ($task['workStatusUuid']==WorkStatus::COMPLETE) $class = "critical3";
                    if ($task['workStatusUuid']==WorkStatus::IN_WORK) $class = "critical5";
                    if ($task['workStatusUuid']==WorkStatus::NEW_OPERATION) $class = "critical1";
                    if ($task['workStatusUuid']==WorkStatus::UN_COMPLETE) $class = "critical2";
                    if ($task['workStatusUuid']==WorkStatus::CANCELED) $class = "critical4";
                    echo '<div class="progress"><div class="'.$class.'">'.$task['workStatus']['title'].'</div></div>';
                    ?></td>
                <td><?= $task['changedAt'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
