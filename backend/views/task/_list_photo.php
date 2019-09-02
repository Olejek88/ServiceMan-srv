<?php
/* @var $photos Photo[] */

use common\models\Photo;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Фотографии</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <tbody>
        <tr>
            <?php foreach ($photos as $photo): ?>
                <td><?= $photo->getImageUrl() . '<br/>' . $photo['changedAt'] ?></td>
            <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>
