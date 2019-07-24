<?php
/* @var $systems */

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Распределение пользователей по домам';

?>
<table id="tree" style="width: 100%">
    <colgroup>
        <col style="width: *">
        <?php
        foreach ($systems as $system) {
            echo '<col style="width: *">';
        }
        ?>
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th colspan="<?= count($systems)+1; ?>" style="text-align:center;background-color: #3c8dbc; color: whitesmoke">Распределение пользователей по домам
        </th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke">
        <th align="center">Адрес
            <button class="btn btn-info" type="button" id="expandButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-expand" aria-hidden="true"></span>
            </button>
            <button class="btn btn-info" type="button" id="collapseButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span>
            </button>
        </th>
        <?php
        foreach ($systems as $system) {
            echo '<th>'.$system['title'].'</th>';
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <?php
        $count=0;
        $idx=[];
        $list='';
        foreach ($systems as $system) {
            if ($count%2) echo '<td class="center"></td>';
            else echo '<td class="alt"></td>';
            $idx[$count]['system'.$system['_id'].'ColumnIdx']=($count+2)."";
            $list.='$tdList.eq('.($count+1).').html(node.data.'.'system'.$system['_id'].')'.PHP_EOL;
            $count++;
        }
        ?>
    </tr>
    </tbody>
</table>

<div class="modal remote fade" id="modalChange">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<?php
$this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js',
    ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
$this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');

try {
    echo FancytreeWidget::widget([
        'options' => [
            'id' => 'tree',
            'source' => $houses,
            'checkbox' => true,
            'selectMode' => 3,
            'extensions' => ['table'],
            'edit' => [
                'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"]
            ],
            'table' => [
                'indentation' => 20,
                "titleColumnIdx" => "1",
                $idx
            ],
            'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            '.$list.'
        }')
        ]
    ]);
} catch (Exception $e) {

}
?>

<div class="modal remote fade" id="modalUser">
    <div class="modal-dialog" style="width: 400px; height: 300px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>

<?php
$this->registerJs('$("#modalUser").on("hidden.bs.modal",
function () {
     window.location.replace("tree");
})');

$this->registerJs('$("#expandButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 4) {
            node.setExpanded(true);
        }
    });
})');

$this->registerJs('$("#collapseButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 4) {
            node.setExpanded(false);
        }
    });
})');
