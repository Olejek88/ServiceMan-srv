<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево типов оборудования и шаблонов задач';

?>
<table id="tree" style="background-color: white; width: 100%">
    <colgroup>
        <col width="*">
        <col width="*">
        <col width="140px">
        <col width="100px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="4" style="background-color: #3c8dbc; color: whitesmoke">
            Задачи для типов оборудования системы
        </th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke; font-weight: normal">
        <th align="center" style="font-weight: normal">Элементы / Шаблоны</th>
        <th>Описание</th>
        <th>Создан</th>
        <th>Норматив</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt" align="center"></td>
        <td></td>
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
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    model: node.data.model,
                                    model_id: node.data.model_id
                                },
                                success: function (data) { 
                                    $(\'#modalAddTemplate\').modal(\'show\');
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
                                          model: data.data.model,
                                          operation: data.data.operation,
                                          model_id: data.data.model_id
                                      },
                                      success: function (result) {
                                          data.remove();            
                                      }                                    
                                   });
                            });
                         }')
                ]
            ]
        ],
        'table' => [
            'indentation' => 20,
            "titleColumnIdx" => "1",
            "lastColumnIdx" => "2",
            "nextColumnIdx" => "3",
            "periodColumnIdx" => "4",
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).text(node.data.last_date);
            $tdList.eq(2).text(node.data.next_date);
            $tdList.eq(3).text(node.data.period);            
        }')
    ]
]);
?>

<div class="modal remote fade" id="modalAddTemplate">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>