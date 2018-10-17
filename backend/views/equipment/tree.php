<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево моделей оборудования';

/* @var $registers common\models\EquipmentRegister */

?>
<script type="text/javascript">
    $("modalDefects").on("click", function (e) {
        //var detailId;

        e.preventDefault();

        // we can use jQuery's built in .data() method to retrieve the detail-id
        //detailId = $(this).data("data-id");
        $('#modalDefects').load('index.php?id=1', function () {
            // call some kind of overlay code that displays the popup?
            // this way the popup will show then the content from popup.php has
            // finished loading.
        });
    });
</script>

<table id="tree">
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

<div class="modal remote fade" id="modalDefects">
    <div class="modal-dialog">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Зафиксированные дефекты</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover text-left">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Пользователь</th>
                        <th>Дефект</th>
                        <th>Тип</th>
                        <th>Время</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--                    <?php /*foreach ($defects as $defect): */ ?>
                        <tr>
                        </tr>
                    --><?php /*endforeach; */ ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal remote fade" id="modalTasks">
    <div class="modal-dialog">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Последние операции над оборудованием</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover text-left">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Пользователь</th>
                        <th>Операция</th>
                        <th>Время</th>
                        <th>Вердикт</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--                    <?php /*foreach ($operations as $operation): */ ?>
                        <tr>
                            <td><? /*= $operation['id'] */ ?></td>
                            <td><? /*= $operation['user'] */ ?></td>
                            <td><? /*= $operation['title'] */ ?></td>
                            <td><? /*= $operation['date'] */ ?></td>
                            <td><? /*= $operation['verdict'] */ ?></td>
                        </tr>
                    --><?php /*endforeach; */ ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalRegister">
    <div class="modal-dialog">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Журнал оборудования</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover text-left">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Пользователь</th>
                        <th>Тип события</th>
                        <th>Время</th>
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