<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево моделей оборудования';

?>
<table id="tree">
    <colgroup>
        <col width="*">
        <col width="120px">
        <col width="130px">
        <col width="120px">
        <col width="120px">
        <col width="130px">
        <col width="70px">
        <col width="*">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="13" style="background-color: #3c8dbc; color: whitesmoke">Оборудование</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke">
        <th align="center">Оборудование</th>
        <th>Статус</th>
        <th>Дата #1</th>
        <th>Показание</th>
        <th>Дата #2</th>
        <th>Показание</th>
        <th>Дата #3</th>
        <th>Показание</th>
        <th>Дата #4</th>
        <th>Показание</th>
        <th>Пользователь</th>
        <th>Фото</th>
        <th>Сообщение</th>
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
            "statusColumnIdx" => "2",
            "dateMeasureColumnIdx" => "3",
            "valueColumnIdx" => "4",
            "dateMeasure2ColumnIdx" => "5",
            "value2ColumnIdx" => "6",
            "dateMeasure3ColumnIdx" => "7",
            "value3ColumnIdx" => "8",
            "dateMeasure4ColumnIdx" => "9",
            "value4ColumnIdx" => "10",
            "userColumnIdx" => "11",
            "photoColumnIdx" => "12",
            "messageColumnIdx" => "13"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.status);
            $tdList.eq(2).html(node.data.measure_date0);
            $tdList.eq(3).text(node.data.measure_value0);
            $tdList.eq(4).html(node.data.measure_date1);
            $tdList.eq(5).text(node.data.measure_value1);
            $tdList.eq(6).html(node.data.measure_date2);
            $tdList.eq(7).text(node.data.measure_value2);
            $tdList.eq(8).html(node.data.measure_date3);
            $tdList.eq(9).text(node.data.measure_value3);
            $tdList.eq(10).html(node.data.measure_user);
            $tdList.eq(11).html(node.data.photo);
            $tdList.eq(12).html(node.data.message);
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