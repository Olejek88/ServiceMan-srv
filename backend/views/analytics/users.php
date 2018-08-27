<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

/* @var string $orders
 * @var string $orders
 * @var string $orders
 * @var string $orders
 *
 */

$this->title = 'Анализ работы пользователей';
?>
<table id="tree" border="1" cellpadding="2" cellspacing="2">
    <colgroup>
        <col width="*">
        <col width="*">
        <col width="150px">
        <col width="120px">
        <col width="120px">
        <col width="120px">
        <col width="120px">
        <col width="120px">
        <col width="120px">
        <col width="120px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr><th align="center">Оператор</th> <th>Должность</th><th>Дата</th><th>Наряды</th><th>Задачи</th>
        <th>Этапы</th><th>Операции</th><th>CEF</th><th>Общее время</th><th>Дефектов</th></tr>
    </thead>
    <tbody>
    <tr>
        <td class="alt" style="padding: 2px"></td>
        <td class="center" style="padding: 2px"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="alt"></td>
    </tr>
    </tbody>
</table>
<br/>
<table border="1" cellpadding="2" cellspacing="2" align="center"><tr><td>
&nbsp; * Наряды/Задачи/Этапы/Операции: выполнено/всего(процент выполненных) <br>
&nbsp; * CEF: процент <br>
&nbsp; * Общее время: выполнения всех операций <br>
&nbsp; * Дефектов: обнаружено дефектов пользователем <br>
</td></tr></table>
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
            "nameColumnIdx" => "1",
            "whoColumnIdx" => "2",
            "dateColumnIdx" => "3",
            "ordersColumnIdx" => "4",
            "tasksColumnIdx" => "5",
            "stagesColumnIdx" => "6",
            "operationsColumnIdx" => "7",
            "ctrColumnIdx" => "8",
            "timeColumnIdx" => "9",
            "defectsColumnIdx" => "10"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(0).text(node.data.name);
            $tdList.eq(1).text(node.data.who);
            $tdList.eq(2).text(node.data.date);
            $tdList.eq(3).text(node.data.orders);
            $tdList.eq(4).text(node.data.tasks);
            $tdList.eq(5).text(node.data.stages);
            $tdList.eq(6).html(node.data.operations);
            $tdList.eq(7).text(node.data.ctr);
            $tdList.eq(8).html(node.data.time);
            $tdList.eq(9).text(node.data.defects);
        }')
    ]
]);
?>
