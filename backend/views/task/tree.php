<?php

use kartik\select2\Select2;
use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево задач';

/* @var $fullTree */

?>
<table id="tree" style="background-color: white; width: 100%">
    <colgroup>
        <col width="*">
        <col width="120px">
        <col width="*">
        <col width="150px">
        <col width="120px">
        <col width="140px">
        <col width="140px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="7" style="background-color: #3c8dbc; color: whitesmoke">Задачи системы</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke; font-weight: normal">
        <th align="center" style="font-weight: normal">Задачи/Операции</th>
        <th>Тип</th>
        <th>Информация</th>
        <th>Исполнитель</th>
        <th>Статус</th>
        <th>Начало</th>
        <th>Конец</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
    </tr>
    </tbody>
</table>
<div class="modal remote fade" id="modal_request">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
<div class="modal remote fade" id="modalChange">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
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
        'source' => $fullTree,
        'checkbox' => true,
        'selectMode' => 3,
        'extensions' => ['table', 'contextMenu'],
        'contextMenu' => [
            'menu' => [
                'delete' => [
                    'name' => "Удалить",
                    'icon' => "delete",
                    'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            $.each(sel, function (event, data) {
                                 $.ajax({
                                      url: "remove",
                                      type: "post",
                                      data: {
                                            level: data.data.level,  
                                            selected_node: data.key,
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
            ]
        ],
        'table' => [
            'indentation' => 20,
            "titleColumnIdx" => "1",
            "typesColumnIdx" => "2",
            "infoColumnIdx" => "3",
            "userColumnIdx" => "4",
            "statusColumnIdx" => "5",
            "startDateColumnIdx" => "6",
            "closeDateColumnIdx" => "7",
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.types);           
            $tdList.eq(2).text(node.data.info);
            $tdList.eq(3).html(node.data.user);
            $tdList.eq(4).html(node.data.status);
            $tdList.eq(5).html(node.data.startDate);
            $tdList.eq(6).html(node.data.closeDate);
        }')
    ]
]);
?>
