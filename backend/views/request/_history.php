<?php
/* @var $registers */

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">История заявки</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Оператор</th>
            <th>Комментарий</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registers as $register): ?>
            <tr>
                <td><?= $register['date'] ?></td>
                <td><?= $register['user']['name'] ?></td>
                <td><?= $register['description'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
