<link href="/css/custom/modules/list/tree.css" rel="stylesheet">

<?php

use backend\assets\AdminLteAsset;

/* @var $parts array */

AdminLteAsset::register($this);
use execut\widget\TreeView;

$groupsContent = TreeView::widget(
    [
        'data' => $parts,
        'size' => TreeView::SIZE_NORMAL,
        'header' => 'Запасные части, расходные материалы',
        'clientOptions' => [
            'selectedBackColor' => 'rgb(40, 153, 57)',
            'borderColor' => '#fff',
            'showTags' => 'true',
            'enableLinks' => 'true',
        ],
    ]
);

$this->title = Yii::t('app', 'Дерево запчастей');
?>
<div style="width:99%; float:left; padding: 3px; line-height: 0.7">
    <?php echo $groupsContent; ?>
</div>

<script type="text/javascript">
    window.onload = function () {
        $('#w0').treeview('collapseAll', {silent: true});
    }
</script>
