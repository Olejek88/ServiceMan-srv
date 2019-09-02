<?php
/* @var $defects */

use common\components\MainFunctions;
use common\models\Request;
use common\models\WorkStatus;
use yii\helpers\Html;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Дефекты</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Дефект</th>
            <th>Тип</th>
            <th>Статус</th>
            <th>Исполнитель</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($defects as $defect): ?>
            <tr>
                <td class="center"><?= $defect['date'] ?></td>
                <td class="center"><?= $defect['title'] ?></td>
                <td class="center"><?= $defect['defectType']['title'] ?></td>
                <td class="center"><?php if ($defect['defectStatus'] == 1) echo 'Обработан'; else echo 'Не обработан'; ?></td>
                <td class="center"><?= $defect['user']['name'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
