<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Анализ шаблонов задач, этапов, операций';

/* @var $orders common\models\Orders */
?>

<table id="tree">
    <colgroup>
        <col width="*">
        <col width="130px">
        <col width="80px">
        <col width="90px">
        <col width="90px">
        <col width="100px">
        <col width="100px">
        <col width="*">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr><th align="center" colspan="9">Анализ нормативов выполнения задач, этапов и операций</th></tr>
    <tr>
        <th align="center">Название</th> <th>Дата</th> <th>Количество</th><th>Ср.время</th>
        <th>Общ.время</th><th>Норматив</th><th>Разница(%)</th><th>Совет</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="alt"></td>
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
            "dateColumnIdx" => "2",
            "quantityColumnIdx" => "3",
            "avgTimeColumnIdx" => "4",
            "sumTimeColumnIdx" => "5",
            "normativeColumnIdx" => "6",
            "differenceColumnIdx" => "7",
            "hintColumnIdx" => "8"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).text(node.data.date);
            $tdList.eq(2).text(node.data.quantity);
            $tdList.eq(3).text(node.data.avgTime);
            $tdList.eq(4).text(node.data.sumTime);
            $tdList.eq(5).text(node.data.normative);
            $tdList.eq(6).html(node.data.difference);
            $tdList.eq(7).text(node.data.hint);
        }')
    ]
]);
?>
