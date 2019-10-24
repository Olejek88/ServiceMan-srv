<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево задач';

/* @var $operations common\models\Operation */
/* @var $users */

?>
<table id="tree" style="background-color: white; width: 100%">
    <colgroup>
        <col width="*">
        <col width="150px">
        <col width="80px">
        <col width="150px">
        <col width="150px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="6" style="background-color: #3c8dbc; color: whitesmoke">
            Технологические карты (шаблоны) для элементов системы
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
        <th>Период</th>
        <th>Создан</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt" align="center"></td>
        <td align="center"></td>
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
        'edit' => [
            'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"],
            'save' => new JsExpression('function(event, data) {
                            setTimeout(function(){
                                $(data.node.span).removeClass("pending");
                                data.node.setTitle(data.node.title);
                            }, 2000);
                            return true;
                        }'),
            'close' => new JsExpression('function(event, data) {
                            if(data.save) {
                                 $(data.node.span).addClass("pending");
                                 $.ajax({
                                    url: "rename",
                                    type: "post",
                                    data: {
                                      uuid: data.node.key,
                                      param: data.node.title,
                                      operation: data.node.data.operation                                         
                                    },
                                    success: function (data) {
                                       }
                                 });
                            }
                        }')
        ],
        'contextMenu' => [
            'menu' => [
                /*                'add' => [
                                    'name' => "Добавить",
                                    'icon' => "add",
                                    'callback' => new JsExpression('function(key, opt) {
                                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                                            var node = $.ui.fancytree.getNode(opt.$trigger);
                                            $.ajax({
                                                url: "add",
                                                type: "post",
                                                data: {
                                                    selected_node: node.key,
                                                    folder: node.folder,
                                                    type_id: node.data.type_id,
                                                    equipment_id: node.data.equipment_id,
                                                    task_id: node.data.task_id
                                                },
                                                success: function (data) {
                                                    $(\'#modalAddOperation\').modal(\'show\');
                                                    $(\'#modalContent\').html(data);
                                                }
                                            });
                                    }')
                                ],*/
                'choose' => [
                    'name' => "Назначить",
                    'icon' => "add",
                    'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "choose",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    type_id: node.data.type_id,
                                    equipment_id: node.data.equipment_id,
                                    task_id: node.data.task_id                                    
                                },
                                success: function (data) { 
                                    $(\'#modalAddOperation\').modal(\'show\');
                                    $(\'#modalContent\').html(data);
                                }
                            });
                    }')
                ],
                'delete' => [
                    'name' => "Удалить",
                    'icon' => "delete",
                    'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            $.each(sel, function (event, data) {
                                var node = $.ui.fancytree.getNode(opt.$trigger);
                                $.ajax({
                                      url: "remove",
                                      type: "post",
                                      data: {
                                            selected_node: node.key,
                                            folder: node.folder,
                                            type_id: node.data.type_id,
                                            equipment_id: node.data.equipment_id,
                                            task_id: node.data.task_id,
                                            task_template_equipment: node.data.task_template_equipment,
                                            task_operation_id: node.data.task_operation_id                             
                                      },
                                      success: function (result) {
                                          console.log(result);
                                          data.remove();            
                                      },
                                      error: function (result) {
                                          console.log(result);
                                          data.remove();            
                                      }                                    
                                   });
                            });
                         }')
                ],
                'edit' => [
                    'name' => 'Редактировать',
                    'icon' => 'edit',
                    'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                        $.ajax({
                            url: "../task-template/edit",
                            type: "post",
                            data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    type_id: node.data.type_id,
                                    equipment_id: node.data.equipment_id,
                                    task_id: node.data.task_id,
                                    task_template_equipment: node.data.task_template_equipment,
                                    operation_id: node.data.operation_id                    
                                },
                                success: function (data) { 
                                    $(\'#modalAddOperation\').modal(\'show\');
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
            "periodColumnIdx" => "4",
            "createdColumnIdx" => "5",
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.types);
            $tdList.eq(2).text(node.data.normative);
            $tdList.eq(3).html(node.data.period);
            $tdList.eq(4).text(node.data.created);            
        }')
    ]
]);

$this->registerJs('$("#modalStatus").on("hidden.bs.modal",
function () {
     window.location.replace("tree");
})');

$this->registerJs('$("#modalAddOperation").on("hidden.bs.modal",
function () {
     window.location.replace("tree");
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

<div class="modal remote fade" id="modalStatus">
    <div class="modal-dialog" style="width: 250px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>

<div class="modal remote fade" id="modalAddOperation">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>

