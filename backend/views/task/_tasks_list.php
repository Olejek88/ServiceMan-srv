<?php
/* @var $stages */

use common\components\MainFunctions;
use common\models\Request;
use common\models\WorkStatus;
use yii\helpers\Html;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">История работ</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Исполнитель</th>
            <th>Задача</th>
            <th>Статус</th>
            <th>Вердикт</th>
            <th>Заявка</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= $task['changedAt'] ?></td>
                <td><?php
                    $users = $task['users'];
                    $users_list="";
                    $cnt=0;
                    foreach ($users as $user) {
                        if ($cnt>0) $users_list .= ',';
                        $users_list .= $user['name'];
                        $cnt++;
                    }
                    if ($cnt>0)
                        echo $users_list;
                    ?></td>
                <td><?= $task['taskTemplate']['title'] ?></td>
                <td><?php
                    $class = "critical2";
                    if ($task['workStatusUuid']==WorkStatus::COMPLETE) $class = "critical3";
                    if ($task['workStatusUuid']==WorkStatus::IN_WORK) $class = "critical5";
                    if ($task['workStatusUuid']==WorkStatus::NEW) $class = "critical1";
                    if ($task['workStatusUuid']==WorkStatus::UN_COMPLETE) $class = "critical2";
                    if ($task['workStatusUuid']==WorkStatus::CANCELED) $class = "critical4";
                    echo '<div class="progress"><div class="'.$class.'">'.$task['workStatus']['title'].'</div></div>';
                    ?></td>
                <td><?php
                    echo MainFunctions::getColorLabelByStatus($task['taskVerdict'], 'task_verdict');
                ?>
                </td>
                <td><?php
                $request = Request::find()->where(['taskUuid' => $task['uuid']])->one();
                if ($request) {
                    $name = "<span class='badge' style='background-color: lightblue; height: 22px'>Заявка #" . $request['_id'] . "</span>";
                    $link = Html::a($name, ['../request/index', 'uuid' => $request['uuid']], ['title' => 'Заявка']);
                    echo $link;
                } else {
                    echo "без заявки";
                }
                ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
