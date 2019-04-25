<?php
/* @var $documentations common\models\Documentation */

use common\components\MyHelpers;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center">Документация на оборудование</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th>Время</th>
            <th>Тип аттрибута</th>
            <th>Параметр</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($documentations as $documentation): ?>
            <tr>
                <td><?= $documentation['title'] ?></td>
                <td><?= $documentation['documentationType']->title ?></td>
                <td><?= Html::a(
                        Html::encode($documentation['path']),
                        Url::to(MyHelpers::getImgUrlPath('/' . $documentation->equipmentUuid . "/" . $documentation['path']))) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
