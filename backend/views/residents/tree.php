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
        <th>Улица</th>
        <th>Дом</th>
        <th>Квартира</th>
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
                "streetColumnIdx" => "1",
                "houseColumnIdx" => "2",
                "flatColumnIdx" => "3",
                "serialColumnIdx" => "4",
                "statusColumnIdx" => "5",
                "valueColumnIdx" => "6"
            ],
            'renderColumns' => new JsExpression(
                'function(event, data) {
                    var node = data.node;
                    $tdList = $(node.tr).find(">td");
                    $tdList.eq(1).text(node.data.street);           
                    $tdList.eq(2).text(node.data.house);
                    $tdList.eq(3).text(node.data.flat);
                    $tdList.eq(4).text(node.data.serial);
                    $tdList.eq(5).html(node.data.status);
                    $tdList.eq(6).text(node.data.value);
                }'
            )
        ]
    ]
);
?>
