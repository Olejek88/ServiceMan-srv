<?php

use kartik\select2\Select2;
use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево файлов системы';

/* @var $files */

?>
<table id="tree" style="background-color: white; width: 100%">
    <colgroup>
        <col width="*">
        <col width="130px">
        <col width="120px">
        <col width="100px">
        <col width="70px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="5" style="background-color: #3c8dbc; color: whitesmoke">Файлы системы</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke; font-weight: normal">
        <th align="center" style="font-weight: normal">Документ</th>
        <th>Дата</th>
        <th>Свойство</th>
        <th>Размер</th>
        <th>Ссылка</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
    </tr>
    </tbody>
</table>
<div class="modal remote fade" id="modal_new">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContent"></div>
    </div>
</div>
<?php
    $this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js',['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
    $this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
                ['depends' => ['yii\jui\JuiAsset']]);
    $this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
    $this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');
    echo FancytreeWidget::widget([
    'options' => [
        'id' => 'tree',
        'source' => $files,
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
                                url: "add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    what: node.data.what,
                                    types: node.data.types
                                },
                                success: function (data) { 
                                    $(\'#modal_new\').modal(\'show\');
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
                                          selected_node: data.key,
                                          folder: node.folder,
                                          type: data.data.types
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
            "dateColumnIdx" => "2",
            "refreshColumnIdx" => "3",
            "sizeColumnIdx" => "4",
            "linksColumnIdx" => "5",
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).text(node.data.date);
            $tdList.eq(2).html(node.data.refresh);           
            $tdList.eq(3).html(node.data.size);
            $tdList.eq(4).html(node.data.links);
        }')
    ]
]);
?>
