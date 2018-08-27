<?php

use backend\assets\AdminLteAsset;

AdminLteAsset::register($this);
use execut\widget\TreeView;

$this->title = 'Дерево нарядов';

/* @var string $customTree */

$groupsContent = TreeView::widget([
    'data' => $customTree,
    'size' => TreeView::SIZE_NORMAL,
    'header' => 'Наряды и задачи сотрудников',
    'searchOptions' => [
        'inputOptions' => [
            'placeholder' => 'Поиск нарядов...'
        ],
    ],
    'clientOptions' => [
        'selectedBackColor' => 'rgb(40, 153, 57)',
        'borderColor' => '#fff',
        'showTags' => 'true',
        'enableLinks' => 'true',
    ],
]);

$fullTree = TreeView::widget([
    'data' => $fullTree,
    'size' => TreeView::SIZE_NORMAL,
    'header' => 'Полное дерево нарядов',
    'searchOptions' => [
        'inputOptions' => [
            'placeholder' => 'Поиск нарядов...'
        ],
    ],
    'clientOptions' => [
        'selectedBackColor' => 'rgb(40, 153, 57)',
        'borderColor' => '#fff',
        'showTags' => 'true',
        'enable-links' => 'true'
    ],
]);
echo '<div>';
echo '<div style="width:49%; float:left">';
echo $groupsContent;
echo '</div>';
echo '<div style="width:50%; float:right">';
echo $fullTree;
echo '</div>';
echo '</div>';

?>
<script type="text/javascript">
    window.onload = function() {
        $('#w0').treeview('collapseAll', {silent: true});
        $('#w2').treeview('collapseAll', {silent: true});
    }
</script>

