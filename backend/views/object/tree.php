<?php
/* @var $contragents */

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево абонентов системы';

?>
<table id="tree" style="width: 100%; font-weight: normal">
    <colgroup>
        <col width="*">
        <col width="*">
        <col width="130px">
        <col width="120px">
        <col width="130px">
        <col width="160px">
        <col width="*">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="7" style="background-color: #3c8dbc; color: whitesmoke">Контрагенты</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke; font-weight: normal">
        <th align="center">Адрес
            <button class="btn btn-info" type="button" id="expandButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-expand" aria-hidden="true"></span>
            </button>
            <button class="btn btn-info" type="button" id="collapseButton" style="padding: 1px 5px">
                <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span>
            </button>
        </th>
        <th align="center">Адрес</th>
        <th>ИНН</th>
        <th>Телефон</th>
        <th>Е-мэйл</th>
        <th>Тип абонента</th>
        <th>Комментарий</th>
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
    </tr>
    </tbody>
</table>

<?php
$this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js', ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
$this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');

echo FancytreeWidget::widget([
    'options' => [
        'id' => 'tree',
        'source' => $contragents,
        'extensions' => ['dnd', "glyph", "table", 'contextMenu'],
        'checkbox' => true,
        'selectMode' => 2,
        'glyph' => 'glyph_opts',
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
                                    source: "../object/tree"
                                },
                                success: function (data) { 
                                    $("#modalAdd").modal("show");
                                    $("#modalContent").html(data);
                                }
                           }); 
                        }                        
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
                                    address: node.data.address,
                                    source: "../object/tree"                                    
                                },
                                success: function (data) { 
                                    $("#modalAdd").modal("show");
                                    $("#modalContent").html(data);
                                }
                           }); 
                    }')
                ],
                'remove' => [
                    'name' => "Отвязать контрагента",
                    'icon' => "delete",
                    'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            $.each(sel, function (event, data) {
                                var node = $.ui.fancytree.getNode(opt.$trigger);
                                $.ajax({
                                      url: "remove-link",
                                      type: "post",
                                      data: {
                                          selected_node: data.key,
                                          type: node.type,
                                          object: node.data.object,
                                          contragent: node.data.uuid
                                      },
                                      success: function (result) {
                                        data.remove();            
                                      }                                    
                                   });
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
                                          selected_node: data.key,
                                          folder: node.folder,
                                          type: node.type,
                                          uuid: node.data.uuid
                                      },
                                      success: function (result) {
                                        data.remove();            
                                      }                                    
                                   });
                            });
                         }')
                ],
            ]
        ],

        'table' => [
            'indentation' => 20,
            "titleColumnIdx" => "1",
            "addressColumnIdx" => "2",
            "innColumnIdx" => "3",
            "phoneColumnIdx" => "4",
            "emailColumnIdx" => "5",
            "contragentTypeColumnIdx" => "6",
            "directorColumnIdx" => "7"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.address);
            $tdList.eq(2).html(node.data.inn);
            $tdList.eq(3).html(node.data.phone);
            $tdList.eq(4).text(node.data.email);
            $tdList.eq(5).html(node.data.contragentType);
            $tdList.eq(6).html(node.data.comment);
        }')
    ]
]);

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

?>

<div class="modal remote" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>
