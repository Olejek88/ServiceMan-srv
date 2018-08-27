<?php

/* @var string $tools */

use backend\assets\AdminLteAsset;

/* @var $tools array */

AdminLteAsset::register($this);
use execut\widget\TreeView;

$groupsContent = TreeView::widget(
    [
        'data' => $tools,
        'size' => TreeView::SIZE_NORMAL,
        'header' => 'Инструменты',
        'clientOptions' => [
            'selectedBackColor' => 'rgb(40, 153, 57)',
            'borderColor' => '#fff',
            'showTags' => 'true',
            'enableLinks' => 'true',
        ],
    ]
);

$this->title = Yii::t('app', 'Дерево инструментов');
?>

<div style="width:99%; float:left; padding: 3px; line-height: 0.7">
    <?php echo $groupsContent; ?>
</div>

<script type="text/javascript">
    window.onload = function () {
        $('#w0').treeview('collapseAll', {silent: true});
    }
</script>

