<?php
/**
 * @var $stageTemplate array
 * @var $equipment array
 * @var $equipmentStage array
 */

use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->title = Yii::t('app', '[2] Связь оборудования с шаблонами этапов и операциями');
?>
<script>
    var currentStage = '0';
</script>

<table style="width: 100%; vertical-align: top; background-color: white">
    <tr style="background-color: #3c8dbc; text-align: center; color: white">
        <td><?php
            echo Html::a(' [3] Этапы операций',
                ['/stage-operation/tree'], ['style' => 'color:white']);
            ?>&nbsp;>&nbsp;<?php
            echo Html::a(' [2] Этапы задач - операции для оборудования',
                ['/equipment-stage/tree'], ['style' => 'color:yellow']);
            ?>&nbsp;>&nbsp;<?php
            echo Html::a(' [1] Этапы задач для оборудования',
                ['/task-equipment-stage/tree'], ['style' => 'color:white']);
            ?>&nbsp;|&nbsp;<?php
            echo Html::a(' [+] Создание задачи',
                ['/task/tree'], ['style' => 'color:white']);
            ?></td>
    </tr>
</table>
<table style="width: 100%; background-color: white; height: 1px">
    <tr><td></td></tr>
</table>

<table style="width: 100%; vertical-align: top; background-color: white" cellpadding="5">
    <thead><tr style="background-color: #3c8dbc; text-align: center; color: white">
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-list"></span> Оборудование',
                ['/equipment'], ['style' => 'color:white']);
            ?></td>
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-menu-hamburger"></span> Этапы задач для оборудования',
                ['/equipment-stage'], ['style' => 'color:white']);
            ?></td>
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-list"></span> Шаблоны этапов',
                ['/stage-operation/tree'], ['style' => 'color:white']);
            ?></td>
    </tr></thead>
    <tr style="vertical-align: top">
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $equipment,
                    'extensions' => ['contextMenu','edit'],
                    'activate' => new JsExpression('function(event, data) {
                                 currentStage = data.node.key;
                                 $.ajax({
                                    url: "check-equipment",
                                    type: "post",
                                    data: {
                                        uuid: data.node.key                                            
                                    }, success: function (data) {
                                        var tree = $("#fancyree_w1").fancytree("getTree");                                        
                                        tree.reload(
                                            JSON.parse(data)
                                        ).done(function() {
                                            var rootNode = $("#fancyree_w1").fancytree("getTree");
                                            tree.visit(function(node){
                                                node.setExpanded(true);
                                            });
                                        });
                                        tree.render();
                                    }
                                 });
                        }'),
                    'edit' => [
                        'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"],
                        'save' => new JsExpression('function(event, data) {
                            setTimeout(function(){
                                $(data.node.span).removeClass("pending");
                                data.node.setTitle(data.node.title);
                            }, 2000);
                            return true;
                        }'),
                        'close' => new JsExpression('function(event, data) {
                            if(data.save) {
                                $(data.node.span).addClass("pending");
                                $.ajax({
                                    url: "edit-equipment",
                                    type: "post",
                                    data: {
                                      uuid: data.node.key,
                                      param: data.node.title                                            
                                    },
                                    success: function (data) {
                                        if (data!=0)
                                            alert (\'Ошибка сохранения названия\');
                                        }
                                 });
                            }
                        }')
                    ],
                    'contextMenu' => [
                        'menu' => [
                            'edit' => [
                                'name' => 'Редактировать',
                                'icon' => 'edit',
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/equipment/update?id="+node.key);                                
                                }')
                            ]
                        ]
                    ]
                ]
            ]);
            ?>
        </td>
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $equipmentStage,
                    'extensions' => ['contextMenu','dnd'],
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
				            if (currentStage>0) {
				                data.otherNode.copyTo(node, data.hitMode);
				                $.ajax({
                                    url: "move-stage",
                                    type: "post",
                                    data: {
                                       uuid: currentStage,
                                       param: data.otherNode.key
                                    }, success: function (data) {
                                        if (data == 0)
                                            data.otherNode.copyTo(node, data.hitMode);
                                        else
                                            alert (\'Невозможно привязать шаблон\');                                         
                                        }
                                })
                            }
                            else alert (\'Выберите оборудование в левом дереве объектов\');                               
			            }'),
                    ],
                    'contextMenu' => [
                        'menu' => [
                            'add' => [
                                'name' => "Добавить",
                                'icon' => "add",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/equipment-stage/create");
                                }')
                            ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    $.ajax({
                                        url: "delete-stage",
                                        type: "post",
                                        data: {
                                            uuid: node.key,
                                            param: 0                                            
                                        },
                                        success: function (data) {
                                            if (data==0)
                                                node.remove();
                                            else
                                                alert(\'Ошибка удаления\');
                                        }
                                    });
                                }')
                            ],
                            'edit' => [
                                'name' => "Редактировать",
                                'icon' => "edit",
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/equipment-stage/update?id="+node.key+"&from=/equipment-stage/tree");                                
                                }')
                            ]
                        ]
                    ]
                ]
            ]);
            ?>
        </td>
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $stageTemplate,
                    'extensions' => ['dnd', 'contextMenu', 'edit'],
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
				            data.otherNode.copyTo(node, data.hitMode);
			            }'),
                    ],
                    'edit' => [
                        'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"],
                        'save' => new JsExpression('function(event, data) {
                            setTimeout(function(){
                                $(data.node.span).removeClass("pending");
                                data.node.setTitle(data.node.title);
                            }, 2000);
                            return true;
                        }'),
                        'close' => new JsExpression('function(event, data) {
                            if(data.save) {
                                $(data.node.span).addClass("pending");
                                $.ajax({
                                    url: "edit-tool",
                                    type: "post",
                                    data: {
                                      uuid: data.node.key,
                                      param: data.node.title                                            
                                    },
                                    success: function (data) {
                                        if (data!=0)
                                            alert (\'Ошибка сохранения названия\');
                                    }
                                });
                            }
                        }')
                    ],
                    'contextMenu' => [
                        'menu' => [
                            'add' => [
                                'name' => "Добавить",
                                'icon' => "add",
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/stage-operation/tree");                                
                                }')
                            ],
                            'edit' => [
                                'name' => 'Редактировать',
                                'icon' => 'edit',
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/stage-template/update?id="+node.key);
                                }')
                            ]
                        ]
                    ]
                ]
            ]);
            ?>
        </td>
    </tr>
</table>

