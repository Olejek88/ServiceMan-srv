<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Подробная статистика по нарядам';

/* @var $orders */
?>

<table id="tree">
    <colgroup>
        <col width="*">
        <col width="150px">
        <col width="100px">
        <col width="150px">
        <col width="150px">
        <col width="60px">
        <col width="60px">
        <col width="60px">
        <col width="*">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr><th align="center">Название</th> <th>Автор</th> <th>Статус</th> <th>Дата начала</th><th>Дата конца</th><th>Время</th><th>Норматив</th><th>%</th><th>Оборудование</th></tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
    </tr>
    </tbody>
</table>
<?php  echo FancytreeWidget::widget([
    'options' =>[
        'id' => 'tree',
        'source' => $orders,
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
            "authorColumnIdx" => "2",
            "orderStatusColumnIdx" => "3",
            "openDateColumnIdx" => "4",
            "closeDateColumnIdx" => "5",
            "timeColumnIdx" => "6",
            "normativeColumnIdx" => "7",
            "differenceColumnIdx" => "8",
            "equipmentColumnIdx" => "9"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).text(node.data.author);
            $tdList.eq(2).text(node.data.orderStatus);
            $tdList.eq(3).text(node.data.openDate);
            $tdList.eq(4).text(node.data.closeDate);
            $tdList.eq(5).text(node.data.time);
            $tdList.eq(6).text(node.data.normative);
            $tdList.eq(7).html(node.data.difference);
            $tdList.eq(8).text(node.data.equipment);
        }')
    ]
]);
?>
