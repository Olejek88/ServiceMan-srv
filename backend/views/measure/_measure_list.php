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
            <th>Исполнитель</th>
            <th>Время</th>
            <th>Значение</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($measures as $measure): ?>
            <tr>
                <td style="text-align: center"><?= $measure['user']['name'] ?></td>
                <td style="text-align: center"><?= $measure['date'] ?></td>
                <td style="text-align: center"><?= $measure['value'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
