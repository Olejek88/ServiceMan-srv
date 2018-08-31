<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

/* @var array $objects */

$this->title = 'Дерево абонентов системы';
?>
<table id="tree">
    <colgroup>
        <col width="*">
        <col width="200px">
        <col width="130px">
        <col width="120px">
        <col width="120px">
        <col width="120px">
        <col width="180px">
        <col width="120px">
        <col width="180px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="10">Объекты системы - оборудование</th>
    </tr>
    <tr>
        <th align="center">Объект</th>
        <th>Тип объекта</th>
        <th>Улица</th>
        <th>Дом</th>
        <th>Квартира</th>
        <th>Оборудование</th>
        <th>Серийный</th>
        <th>Статус</th>
        <th>Значение</th>
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
<?php echo FancytreeWidget::widget(
    [
        'options' => [
            'id' => 'tree',
            'source' => $objects,
            'extensions' => ['dnd', "glyph", "table"],
            'glyph' => 'glyph_opts',
            'dnd' => [
                'preventVoidMoves' => true,
                'preventRecursiveMoves' => true,
                'autoExpandMS' => 400,
                'dragStart' => new JsExpression(
                    'function(node, data) {
				        return true;
			        }'
                ),
                'dragEnter' => new JsExpression(
                    'function(node, data) {
				        return true;
			        }'
                ),
                'dragDrop' => new JsExpression(
                    'function(node, data) {
				        data.otherNode.moveTo(node, data.hitMode);
			        }'
                ),
            ],
            'table' => [
                'indentation' => 20,
                "titleColumnIdx" => "1",
                "typeColumnIdx" => "2",
                "dateColumnIdx" => "3",
                "inventoryColumnIdx" => "4",
                "serialColumnIdx" => "5",
                "statusColumnIdx" => "6",
                "tagColumnIdx" => "7"
            ],
            'renderColumns' => new JsExpression(
                'function(event, data) {
                    var node = data.node;
                    $tdList = $(node.tr).find(">td");
                    $tdList.eq(1).text(node.data.type);
                    $tdList.eq(2).text(node.data.street);           
                    $tdList.eq(3).text(node.data.house);
                    $tdList.eq(4).text(node.data.flat);
                    $tdList.eq(5).text(node.data.equipment);
                    $tdList.eq(6).text(node.data.serial);
                    $tdList.eq(7).html(node.data.status);
                    $tdList.eq(8).text(node.data.value);
                }'
            )
        ]
    ]
);
?>
