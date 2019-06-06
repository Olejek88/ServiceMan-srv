<?php
/* @var $registers common\models\EquipmentRegister */
/* @var $equipmentUuid  */
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Журнал событий</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Время</th>
            <th>Пользователь</th>
            <th>Тип события</th>
            <th>Описание</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($registers as $register): ?>
            <tr>
                <td><?= $register['date'] ?></td>
                <td><?= $register['user']['name'] ?></td>
                <td><?= $register['registerType']->title ?></td>
                <td><?= $register['description'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
