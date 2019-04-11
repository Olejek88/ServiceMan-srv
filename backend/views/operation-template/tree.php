
<?php
$this->title = Yii::t('app', 'Дерево шаблонов операций');

use backend\assets\AdminLteAsset;

/* @var $this yii\web\View */
/* @var $templates common\models\OperationTemplate[] */

AdminLteAsset::register($this);
use execut\widget\TreeView;

$groupsContent = TreeView::widget(
    [
        'data' => $templates,
        'size' => TreeView::SIZE_NORMAL,
        'header' => 'Шаблоны задач',
        'clientOptions' => [
            'selectedBackColor' => 'rgb(40, 153, 57)',
            'borderColor' => '#fff',
            'showTags' => 'true',
            'enableLinks' => 'true',
        ],
    ]
);
?>

<div style="width:99%; float:left; padding: 3px; line-height: 0.7">
<?php echo $groupsContent; ?>
</div>

<script type="text/javascript">
    window.onload = function () {
        $('#w0').treeview('collapseAll', {silent: true});
    }
</script>

