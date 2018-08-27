<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.2.3/jquery.contextMenu.min.css"/>

<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

/* @var $equipmentTree array */
/* @var $ordersTree array */
/* @var $taskEquipmentStageTree array */

$this->title = Yii::t('app', '[+] Создание задачи и назначение наряду');
?>
<script src="/js/custom/modules/list/jquery.js"></script>
<script src="/js/custom/modules/list/jquery-ui.custom.js"></script>
<script>
    var currentStage = '0';
    var currentTemplate = '0';
    var currentTaskES = '0';
    var taskTemplate = 0;
</script>

<table style="width: 100%; vertical-align: top; background-color: white">
    <tr style="background-color: #3c8dbc; text-align: center; color: white">
        <td><?php
            echo Html::a(' [3] Этапы операций',
                ['/stage-operation/tree'], ['style' => 'color:white']);
            ?>&nbsp;>&nbsp;<?php
            echo Html::a(' [2] Этапы задач для оборудования',
                ['/equipment-stage/tree'], ['style' => 'color:white']);
            ?>&nbsp;>&nbsp;<?php
            echo Html::a(' [1] Этапы задач для оборудования',
                ['/task-equipment-stage/tree'], ['style' => 'color:white']);
            ?>&nbsp;|&nbsp;<?php
            echo Html::a(' [+] Создание задачи',
                ['/task/tree'], ['style' => 'color:yellow']);
            ?></td>
    </tr>
</table>
<table style="width: 100%; background-color: white; height: 1px">
    <tr><td></td></tr>
</table>

<table style="width: 100%; vertical-align: top; background-color: white" cellpadding="5">
    <thead><tr style="background-color: #3c8dbc; text-align: center; color: white">
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-list"></span> Шаблоны задач',
                ['/task-template'], ['style' => 'color:white']);
            ?></td>
        <td><?php
            echo '<span class="glyphicon glyphicon-menu-hamburger"></span> Состав задачи';
            ?></td>
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-list"></span> Наряды',
                ['/orders/table'], ['style' => 'color:white']);
            ?></td>
    </tr></thead>
    <tr style="vertical-align: top">
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $equipmentTree,
                    'extensions' => ['contextMenu','edit'],
                    'activate' => new JsExpression('function(event, data) {
                                currentStage = data.node.key;
                                $.ajax({
                                    url: "check-task",
                                    type: "post",
                                    data: {
                                        uuid: data.node.key                                            
                                    }, success: function (data) {                                    
                                        var tree = $("#fancyree_w1").fancytree("getTree");
                                        if (data!=-1) {                                        
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
                                    }
                            });
                        }'),
                    'contextMenu' => [
                        'menu' => [
                            'add' => [
                                'name' => "Добавить",
                                'icon' => "add",
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/task-equipment-stage/update?id="+node.key);                                
                                }')
                            ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    $.ajax({
                                        url: "delete-task",
                                        type: "post",
                                        data: {
                                            uuid: node.key,
                                            param: 0                                            
                                        },
                                        success: function (data) {
                                            if(data == 0) 
                                                node.remove();
                                            else
                                                alert (\'Этот элемент удалить нельзя\');
                                        }
                                   });
                                }')
                            ],
                            'edit' => [
                                'name' => 'Редактировать',
                                'icon' => 'edit',
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/task-equipment-stage/update?id="+node.key);                                
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
                    'source' => $taskEquipmentStageTree,
                    'extensions' => ['dnd'],
                    'activate' => new JsExpression('function(event, data) {
                        if (data.node.folder) {
                            currentTemplate = data.node.key;                            
                            taskTemplate = 1;
                        }
                        else {
                            currentTaskSE = data.node.key;
                            taskTemplate = 0;
                        }
                    }'),

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
                ]
            ]);
            ?>
        </td>
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $ordersTree,
                    'extensions' => ['dnd', 'contextMenu'],
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
				                if (data.node.folder==1 && data.node.data.order) {
                                    var datas = data;
				                    $.ajax({
                                        url: "move-task",
                                        type: "post",
                                        data: {
                                            uuidTemplate: currentTemplate,
                                            uuidTask: currentTaskES,
                                            taskTemplate: taskTemplate,
                                            orderUuid: datas.node.key,
                                            currentTS: currentStage
                                        }, success: function (data) {
                                            if (data == 0)
                                                datas.otherNode.moveTo(node, datas.hitMode);
                                            else
                                                alert (\'Невозможно добавить задачу\');                                         
                                            }
                                    })
                                }
                                else alert (\'Нельзя добавить задачу в это место\');
                            }
                            else alert (\'Выберите задачу в левом дереве объектов\');                               
			            }'),
                    ],
                    'contextMenu' => [
                        'menu' => [
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    if (node.folder) {
                                        $.ajax({
                                            url: "delete-task-stage",
                                            type: "post",
                                            data: {
                                                uuid: node.key,
                                                param: 0                                            
                                            },
                                            success: function (data) {
                                                if(data == 0) 
                                                    node.remove();
                                                else
                                                    alert (\'Этот элемент удалить нельзя\');
                                            }
                                        });
                                    }
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

