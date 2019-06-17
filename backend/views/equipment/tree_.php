<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево оборудования';

/* @var $registers common\models\Equipment */

?>
<table id="tree" style="width: 100%">
    <colgroup>
        <col width="*">
        <col width="100px">
        <col width="120px">
        <col width="130px">
        <col width="80px">
        <col width="120px">
        <col width="130px">
        <col width="120px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="11" style="background-color: #3c8dbc; color: whitesmoke">Оборудование</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke">
        <th align="center">Оборудование</th>
        <th>Серийный</th>
        <th>Статус</th>
        <th>Дата обхода</th>
        <th>Показания</th>
        <th>Пользователь</th>
        <th>Дата фото</th>
        <th>Фото</th>
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
    </tr>
    </tbody>
</table>
<?php echo FancytreeWidget::widget([
    'options' => [
        'id' => 'tree',
        'source' => $equipment,
        'extensions' => ['dnd', "glyph", "table", 'contextMenu'],
        'checkbox' => true,
        'selectMode' => 3,
        'glyph' => 'glyph_opts',
        'dnd' => [
            'preventVoidMoves' => true,
            'preventRecursiveMoves' => true,
            'autoExpandMS' => 400,
            'dragStart' => new JsExpression('function(node, data) {
				return true;
			}'),
            'dragEnter' => new JsExpression('function(node, data) {
				return true;
			}'),
            'dragDrop' => new JsExpression('function(node, data) {
				data.otherNode.moveTo(node, data.hitMode);
			}'),
        ],
        'table' => [
            'indentation' => 20,
            "titleColumnIdx" => "1",
            "serialColumnIdx" => "2",
            "statusColumnIdx" => "3",
            "dateMeasureColumnIdx" => "4",
            "valueColumnIdx" => "5",
            "userColumnIdx" => "6",
            "datePhotoColumnIdx" => "7",
            "photoColumnIdx" => "8"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).text(node.data.serial);
            $tdList.eq(2).html(node.data.status);
            $tdList.eq(3).html(node.data.measure_date);
            $tdList.eq(4).text(node.data.measure_value);
            $tdList.eq(5).html(node.data.measure_user);
            $tdList.eq(6).html(node.data.photo_date);
            $tdList.eq(7).html(node.data.photo);
        }')
    ]
]);
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
    <div class="modal-dialog" style="width: 400px; height: 300px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
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
        <div class="modal-content loader-lg" id="modalContent">
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
<div class="modal remote fade" id="modalRequest">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentRequest"></div>
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
$this->registerJs('$("#modalSN").on("hidden.bs.modal",
function () {
     window.location.replace("tree-street");
})');
$this->registerJs('$("#modalAddTask").on("hidden.bs.modal",
function () {
     window.location.replace("tree-street");
})');
$this->registerJs('$("#modalStatus").on("hidden.bs.modal",
function () {
     window.location.replace("tree-street");
})');
$this->registerJs('$("#modalRequest").on("hidden.bs.modal",
function () {
     $(this).removeData();
     window.location.replace("tree-street");
})');

$this->registerJs('$("#addButton").on("click",function() {
        var sel = $.ui.fancytree.getTree().getSelectedNodes();
        var count = $(sel).length;
        var i = 0;        
        $.each(sel, function (event, data) {
            if (data.folder==true && data.type=="house") {
                $.ajax({
                    url: "move",
                    type: "post",
                    data: {
                        selected_node: data.data.uuid,
                        user: $("#user_select").val()
                    },
                    success: function (data) {
                        i = i + 1;
                        if (i === count) {
                            window.location.replace("tree-street");
                        }                    
                    }
                });
            }
        });
    })');
$this->registerJs('$("#removeButton").on("click",function() {
        var sel = $.ui.fancytree.getTree().getSelectedNodes();
        var count = $(sel).length;
        var i = 0;        
        $.each(sel, function (event, data) {
            if (data.folder==true && node.type=="house") {
                $.ajax({
                    url: "remove",
                    type: "post",
                    data: {
                        selected_node: data.key,
                    },
                    success: function (data) {
                        i = i + 1;
                        if (i === count) {
                            window.location.replace("tree-street");
                        }                    
                    }                  
                });
            }
        });
    })');

?>
