<?php
/* @var $comments common\models\Comments */

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Сообщения по запросу</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Время</th>
            <th>Сообщение&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($comments as $comment): ?>
            <tr>
                <td style="text-align: center"><?= $comment['date'] ?></td>
                <td><?= $comment['text'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
