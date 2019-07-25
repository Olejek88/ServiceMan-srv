<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево технологических карт';

/* @var $users */

?>
<table id="tree" style="background-color: white; width: 100%">
    <colgroup>
        <col width="*">
        <col width="*">
        <col width="100px">
        <col width="150px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="4" style="background-color: #3c8dbc; color: whitesmoke">
            Технологические карты (шаблоны) для типов оборудования системы
        </th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke; font-weight: normal">
        <th align="center" style="font-weight: normal">Элементы / Шаблоны
            <button class="btn btn-info" type="button" id="expandButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-expand" aria-hidden="true"></span>
            </button>
            <button class="btn btn-info" type="button" id="collapseButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span>
            </button>
        </th>
        <th>Тип</th>
        <th>Норматив</th>
        <th>Создан</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt" align="center"></td>
        <td align="center"></td>
        <td class="alt" align="center"></td>
    </tr>
    </tbody>
</table>
<?php
    $this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js',
        ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
    $this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
        ['depends' => ['yii\jui\JuiAsset']]);
    $this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
    $this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');

    echo FancytreeWidget::widget([
    'options' => [
        'id' => 'tree',
        'source' => $equipment,
        'checkbox' => true,
        'selectMode' => 3,
        'extensions' => ['table', 'contextMenu'],
        'contextMenu' => [
            'menu' => [
                'add' => [
                    'name' => "Добавить",
                    'icon' => "add",
                    'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "add-template",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    type_id: node.data.type_id,
                                    types_id: node.data.types_id,
                                    task_id: node.data.task_id                                   
                                },
                                success: function (data) { 
                                    $(\'#modalAddTask\').modal(\'show\');
                                    $(\'#modalContent\').html(data);
                                }
                            });
                    }')
                ],
                'delete' => [
                    'name' => "Удалить",
                    'icon' => "delete",
                    'callback' => new JsExpression('function(key, opt) {
                                var node = $.ui.fancytree.getNode(opt.$trigger);
                                $.ajax({
                                      url: "remove-template",
                                      type: "post",
                                      data: {
                                            selected_node: node.key,
                                            folder: node.folder,
                                            type_id: node.data.type_id,
                                            task_id: node.data.task_id
                                      },
                                      success: function (result) {
                                          console.log(result);
                                          node.remove();            
                                      },
                                      error: function (result) {
                                          console.log(result);
                                          node.remove();            
                                      }                                    
                                   });
                         }')
                ],
                'edit' => [
                    'name' => 'Редактировать',
                    'icon' => 'edit',
                    'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                        $.ajax({
                            url: "edit-template",
                            type: "post",
                            data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    type_id: node.data.type_id,
                                    task_id: node.data.task_id
                                },
                                success: function (data) { 
                                    $(\'#modalAddTask\').modal(\'show\');
                                    $(\'#modalContent\').html(data);
                                }
                           }); 
                    }')
                ]
            ]
        ],
        'table' => [
            'indentation' => 20,
            "titleColumnIdx" => "1",
            "typesColumnIdx" => "2",
            "normativeColumnIdx" => "3",
            "createdColumnIdx" => "4",
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.types);
            $tdList.eq(2).text(node.data.normative);
            $tdList.eq(3).text(node.data.created);            
        }')
    ]
]);

$this->registerJs('$("#modalAddTask").on("hidden.bs.modal",
function () {
    window.location.replace("tree-type");
})');

$this->registerJs('$("#expandButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 5) {
            node.setExpanded(true);
        }
    });
})');

$this->registerJs('$("#collapseButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 5) {
            node.setExpanded(false);
        }
    });
})');
?>

<div class="modal remote fade" id="modalAddTask">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>

