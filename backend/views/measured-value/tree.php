<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево моделей оборудования - измеренные значения';
?>
<table id="tree">
    <colgroup>
        <col width="*">
        <col width="150px">
        <col width="150px">
        <col width="150px">
        <col width="100px">
        <col width="*">
    </colgroup>
    <thead style="background-color: #337ab7">
    <tr><th align="center" colspan="10">Измеренные значения</th></tr>
    <tr><th align="center">Оборудование</th> <th>Расположение</th><th>Параметр</th><th>Дата измерения</th><th>Значение</th><th>Операция</th></tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
    </tr>
    </tbody>
</table>
<?php  echo FancytreeWidget::widget([
    'options' =>[
        'id' => 'tree',
        'source' => $equipment,
        'extensions' => ['dnd', "glyph", "table"],
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
            "locationColumnIdx" => "2",
            "parameterColumnIdx" => "3",
            "dateColumnIdx" => "4",
            "valueColumnIdx" => "5",
            "operationColumnIdx" => "6"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).text(node.data.location);
            $tdList.eq(2).html(node.data.parameter);           
            $tdList.eq(3).text(node.data.date);
            $tdList.eq(4).text(node.data.value);
            $tdList.eq(5).html(node.data.operation);
        }')
    ]
]);
?>