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
            <th>Пользователь</th>
            <th>Дефект</th>
            <th>Тип дефекта</th>
            <th>Статус</th>
            <th>Время</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($defects as $defect): ?>
            <tr>
                <td><?= $defect['user']['name'] ?></td>
                <td><?= $defect['title'] ?></td>
                <td><?= $defect['defectType']['title'] ?></td>
                <td><?php
                    if ($model['defectStatus'])
                        echo '<div class="progress"><div class="critical5">Обработан</div></div>';
                    else
                        echo '<div class="progress"><div class="critical1">Не обработан</div></div>';
                    ?>
                </td>
                <td><?= $defect['date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
