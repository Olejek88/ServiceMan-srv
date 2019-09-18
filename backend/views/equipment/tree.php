<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево моделей элементов';

?>
<table id="tree" style="width: 100%">
    <colgroup>
        <col style="width:*">
        <col style="width:*">
        <col style="width:180px">
        <col style="width:120px">
        <col style="width:130px">
        <col style="width:150px">
        <col style="width:110px">
        <col style="width:80px">
        <col style="width:80px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th colspan="10" style="text-align:center;background-color: #3c8dbc; color: whitesmoke">Элементы системы</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke">
        <th align="center">Элементы
            <button class="btn btn-info" type="button" id="expandButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-expand" aria-hidden="true"></span>
            </button>
            <button class="btn btn-info" type="button" id="collapseButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span>
            </button>
        </th>
        <th>Адрес</th>
        <th>Задачи</th>
        <th>Заводской номер</th>
        <th>Статус</th>
        <th>Исполнители</th>
        <th>Дата ввода в эксплуатацию</th>
        <th>Файлы</th>
        <th>Действия</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
    </tr>
    </tbody>
</table>

<div class="modal remote fade" id="modalChange">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<?php
$this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js', ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
$this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');
try {
    echo FancytreeWidget::widget([
        'options' => [
            'id' => 'tree',
            'source' => $equipment,
            'checkbox' => true,
            'selectMode' => 2,
            'extensions' => ['table', 'contextMenu'],
            'edit' => [
                'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"],
                'close' => new JsExpression('function(event, data) {
                            if(data.save) {
                                 $(data.node.span).addClass("pending");
                                 $.ajax({
                                    url: "rename",
                                    type: "post",
                                    data: {
                                      uuid: data.node.key,
                                      folder: data.node.folder,
                                      param: data.node.title                                            
                                    },
                                    success: function (data) {
                                       }
                                 });
                            }
                        }')
            ],
            'contextMenu' => [
                'menu' => [
                    'new' => [
                        'name' => 'Добавить',
                        'icon' => 'add',
                        'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                        if (node.folder==true) {
                            $.ajax({
                                url: "new",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.type,
                                    model_uuid: node.data.model_uuid,
                                    type_uuid: node.data.type_uuid,
                                    source: "../equipment/tree"                                                                                                            
                                },
                                success: function (data) { 
                                    $(\'#modalAddEquipment\').modal(\'show\');
                                    $(\'#modalContentEquipment\').html(data);
                                }
                           }); 
                        }                        
                    }')
                    ],
                    'delete' => [
                        'name' => 'Удалить',
                        'icon' => 'delete',
                        'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            $.each(sel, function (event, data) {
                                 $.ajax({
                                      url: "deleted",
                                      type: "post",
                                      data: {
                                            type: data.type,
                                            selected_node: data.data.uuid
                                      },
                                    error: function (result) {
                                        console.log(result);                                 
                                    },
                                    success: function (result) {
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
                                url: "edit",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.type,
                                    model_uuid: node.data.model_uuid,
                                    type_uuid: node.data.type_uuid,
                                    reference: "equipment",
                                    source: "../equipment/tree"                                                                        
                                },
                                success: function (data) { 
                                    $(\'#modalAddEquipment\').modal(\'show\');
                                    $(\'#modalContentEquipment\').html(data);
                                }
                           }); 
                    }')
                    ],
                    'doc' => [
                        'name' => 'Добавить документацию',
                        'icon' => 'add',
                        'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            console.log(node);
                            $.ajax({
                                url: "../documentation/add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.data.type,
                                    model_uuid: node.data.model_uuid,
                                    source: "../equipment/tree"                                                                        
                                },
                                success: function (data) { 
                                    $(\'#modalAddDocumentation\').modal(\'show\');
                                    $(\'#modalContentDoc\').html(data);
                                }
                            });
                    }')
                    ],
                    'defect' => [
                        'name' => 'Добавить дефект',
                        'icon' => 'add',
                        'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "../defect/add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    model_uuid: node.data.model_uuid,
                                    source: "../equipment/tree"                                                                                                                                                
                                },
                                success: function (data) { 
                                    $(\'#modalAddDefect\').modal(\'show\');
                                    $(\'#modalContentDefect\').html(data);
                                }
                            });
                    }')
                    ],
                    'task' => [
                        'name' => 'Периодическая задача',
                        'icon' => 'add',
                        'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "../task/add-periodic",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    model_uuid: node.data.model_uuid,
                                    type_uuid: node.data.type_uuid,
                                    source: "../equipment/tree"                                                                                                            
                                },
                                success: function (data) { 
                                    $(\'#modalAddPeriodicTask\').modal(\'show\');
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
                "locationColumnIdx" => "2",
                "tasksColumnIdx" => "3",
                "serialColumnIdx" => "4",
                "statusColumnIdx" => "5",
                "userColumnIdx" => "6",
                "startColumnIdx" => "7",
                "docsColumnIdx" => "8",
                "linksColumnIdx" => "9",
            ],
            'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.location);
            $tdList.eq(2).html(node.data.tasks);
            $tdList.eq(3).html(node.data.serial);
            $tdList.eq(4).html(node.data.status);
            $tdList.eq(5).html(node.data.user);
            $tdList.eq(6).html(node.data.start);
            $tdList.eq(7).html(node.data.docs);
            $tdList.eq(8).html(node.data.links);
        }')
        ]
    ]);
} catch (Exception $e) {

}
?>

<div class="modal remote fade" id="modalMeasures">
    <div class="modal-dialog" style="width: 600px">
        <div class="modal-content loader-lg">
        </div>
    </div>
</div>

<div class="modal remote fade" id="modalTasks">
    <div class="modal-dialog" style="width: 1000px">
        <div class="modal-content loader-lg">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalRegister">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg" id="modalRegisterContent">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalStatus">
    <div class="modal-dialog" style="width: 250px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalSN">
    <div class="modal-dialog" style="width: 250px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAddTask">
    <div class="modal-dialog" style="width: 800px; height: 400px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAddPeriodicTask">
    <div class="modal-dialog" style="width: 600px; height: 300px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAttributes">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAddDocumentation">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentDoc">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAddDefect">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentDefect">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAddEquipment">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentEquipment">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalDefects">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContent"></div>
    </div>
</div>
<div class="modal remote fade" id="modalRequest">
    <div class="modal-dialog" style="width: 1000px; height: 400px">
        <div class="modal-content loader-lg" id="modalContentRequest"></div>
    </div>
</div>
<div class="modal remote fade" id="modalMeasure">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentMeasure"></div>
    </div>
</div>

<?php
$this->registerJs('$("#modalRegister").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalTasks").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalDefects").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalMeasures").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalSN").on("hidden.bs.modal",
function () {
     window.location.replace("tree");
})');
$this->registerJs('$("#modalAddTask").on("hidden.bs.modal",
function () {
})');
$this->registerJs('$("#modalStatus").on("hidden.bs.modal",
function () {
     window.location.replace("tree");
})');
$this->registerJs('$("#modalRequest").on("hidden.bs.modal",
function () {
     $(this).removeData();
     window.location.replace("tree");
})');

$this->registerJs('$("#expandButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 2) {
            node.setExpanded(true);
        }
    });
})');

$this->registerJs('$("#collapseButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 2) {
            node.setExpanded(false);
        }
    });
})');
