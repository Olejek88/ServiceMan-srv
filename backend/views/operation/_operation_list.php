<?php
/* @var $stages */

use common\models\StageStatus;

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
            <th>Наряд</th>
            <th>Этап</th>
            <th>Статус</th>
            <th>Дата</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stages as $stage): ?>
            <tr>
                <td><?= $stage['task']['order']['user']['name'] ?></td>
                <td><?= $stage['task']['order']['title'] ?></td>
                <td><?= $stage['stageTemplate']['title'] ?></td>
                <td><?php
                    if ($stage['stageStatusUuid']==StageStatus::COMPLETE) $class = "critical3";
                    if ($stage['stageStatusUuid']==StageStatus::IN_WORK) $class = "critical5";
                    if ($stage['stageStatusUuid']==StageStatus::NEW_TASK) $class = "critical1";
                    if ($stage['stageStatusUuid']==StageStatus::UN_COMPLETE) $class = "critical2";
                    if ($stage['stageStatusUuid']==StageStatus::CANCELED) $class = "critical4";
                    echo '<div class="progress"><div class="'.$class.'">'.$stage['stageStatus']['title'].'</div></div>';
                    ?></td>
                <td><?= $stage['changedAt'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
