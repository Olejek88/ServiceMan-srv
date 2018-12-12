<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево моделей оборудования';

?>
<table id="tree">
    <colgroup>
        <col width="*">
        <col width="130px">
        <col width="80px">
        <col width="130px">
        <col width="80px">
        <col width="130px">
        <col width="80px">
        <col width="130px">
        <col width="80px">
        <col width="80px">
        <col width="80px">
        <col width="80px">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="12" style="background-color: #3c8dbc; color: whitesmoke">Оборудование</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke">
        <th align="center">Адрес</th>
        <th>Дата</th>
        <th>#1</th>
        <th>Дата</th>
        <th>#2</th>
        <th>Дата</th>
        <th>#3</th>
        <th>Дата</th>
        <th>#4</th>
        <th>Разница</th>
        <th>Интервал</th>
        <th>Удельная</th>
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
            "dateMeasureColumnIdx" => "2",
            "valueColumnIdx" => "3",
            "dateMeasure2ColumnIdx" => "4",
            "value2ColumnIdx" => "5",
            "dateMeasure3ColumnIdx" => "6",
            "value3ColumnIdx" => "7",
            "dateMeasure4ColumnIdx" => "8",
            "value4ColumnIdx" => "9",
            "diffColumnIdx" => "10",
            "intervalColumnIdx" => "11",
            "relativeColumnIdx" => "12"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.measure_date0);
            $tdList.eq(2).text(node.data.measure_value0);
            $tdList.eq(3).html(node.data.measure_date1);
            $tdList.eq(4).text(node.data.measure_value1);
            $tdList.eq(5).html(node.data.measure_date2);
            $tdList.eq(6).text(node.data.measure_value2);
            $tdList.eq(7).html(node.data.measure_date3);
            $tdList.eq(8).text(node.data.measure_value3);
            $tdList.eq(9).html(node.data.value);
            $tdList.eq(10).html(node.data.interval);
            $tdList.eq(11).html(node.data.relative);
        }')
    ]
]);
?>


<div class="modal remote fade" id="modalRegister">
    <div class="modal-dialog">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Показания</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover text-left">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Время</th>
                        <th>Значение</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--                    <?php /*foreach ($registers as $register): */ ?>
                        <tr>
                            <td><? /*= $register['uuid'] */ ?></td>
                            <td><? /*= $register['user'] */ ?></td>
                            <td><? /*= $register['type'] */ ?></td>
                            <td><? /*= $register['date'] */ ?></td>
                        </tr>
                    --><?php /*endforeach; */ ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>