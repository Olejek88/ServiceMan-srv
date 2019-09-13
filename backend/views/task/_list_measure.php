<?php
/* @var $measures */

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Измерения</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Оборудование</th>
            <th>Тип измерения</th>
            <th>Значение</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($measures as $measure): ?>
            <tr>
                <td class="center"><?= $measure['date'] ?></td>
                <td class="center"><?= $measure['equipment']['title'] . ' [' . $measure['equipment']['serial'] . ']' ?></td>
                <td class="center"><?= $measure['measureType']['title'] ?></td>
                <td class="center"><?= "<span class='badge' style='background-color: green; height: 22px; margin-top: -3px'>" . $measure['value'] . "</span>" ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
