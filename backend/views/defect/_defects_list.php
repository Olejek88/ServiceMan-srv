<?php
/* @var $defects common\models\Defect */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Зафиксированные дефекты</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>#</th>
            <th>Пользователь</th>
            <th>Элементов</th>
            <th>Дефект</th>
            <th>Тип</th>
            <th>Время</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($defects as $defect): ?>
            <tr>
                <td><?= $defect['_id'] ?></td>
                <td><?= $defect['user']['name'] ?></td>
                <td><?= $defect['equipment']['title'] ?></td>
                <td><?= $defect['title'] ?></td>
                <td><?= $defect['defectStatus'] ?></td>
                <td><?= $defect['date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
