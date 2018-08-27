<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.2.3/jquery.contextMenu.min.css"/>

<?php
/**
 * @var $stageTemplate array
 * @var $operationTemplate array
 * @var $select array
 */

use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->title = Yii::t('app', '[3] Связь шаблонов этапов и операций');
?>
<script src="/js/custom/modules/list/jquery.js"></script>
<script src="/js/custom/modules/list/jquery-ui.custom.js"></script>
<script>
    var currentStage = '0';
</script>

<table style="width: 100%; vertical-align: top; background-color: white">
    <tr style="background-color: #3c8dbc; text-align: center; color: white">
        <td><?php
            echo Html::a(' [3] Этапы операций',
                ['/stage-operation/tree'], ['style' => 'color:yellow']);
            ?>&nbsp;>&nbsp;<?php
            echo Html::a(' [2] Этапы задач для оборудования',
                ['/equipment-stage/tree'], ['style' => 'color:white']);
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
            echo Html::a('<span class="glyphicon glyphicon-list"></span> Шаблоны этапов задач',
                ['/stage-template'], ['style' => 'color:white']);
            ?></td>
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-menu-hamburger"></span> Связи этапов и операций',
                ['/stage-operation'], ['style' => 'color:white']);
            ?></td>
        <td><?php
            echo Html::a('<span class="glyphicon glyphicon-list"></span> Шаблоны операций',
                ['/operation-template'], ['style' => 'color:white']);
            ?></td>
    </tr></thead>
    <tr style="vertical-align: top">
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $stageTemplate,
                    'extensions' => ['contextMenu','edit'],
                    'activate' => new JsExpression('function(event, data) {
                                 currentStage = data.node.key;
                                 $.ajax({
                                    url: "move",
                                    type: "post",
                                    data: {
                                        action: 5,
                                        folder: data.node.folder, 
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
                                    url: "move",
                                    type: "post",
                                    data: {
                                      action: 8, 
                                      uuid: data.node.key,
                                      param: data.node.title                                            
                                    },
                                    success: function (data) {
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
                                     if (node.folder)
                                        var folder = node;
                                     else
                                        var folder = node.parent;                                        
                                     $.ajax({
                                        url: "move",
                                        type: "post",
                                        data: {
                                            action: 7, 
                                            param: folder.key                                            
                                        },
                                        success: function (data) {
                                            if (data>0) {
                                                newNode = folder.addNode({title:\'Новый шаблон\'});
                                                newNode.key = data;
                                            }
                                        }
                                     });                                     
                                    }')
                                ],
                            'copy' => [
                                'name' => "Копировать",
                                'icon' => "copy",
                                'callback' =>new JsExpression('function(key, opt) {
                                     var node = $.ui.fancytree.getNode(opt.$trigger);
                                    }')
                                ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                     var node = $.ui.fancytree.getNode(opt.$trigger);
                                     $.ajax({
                                        url: "move",
                                        type: "post",
                                        data: {
                                            action: 6, 
                                            uuid: node.key,
                                            param: 0                                            
                                        },
                                        success: function (data) {
                                            node.remove();
                                        }
                                     });
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
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $select,
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
                                    url: "move",
                                    type: "post",
                                    data: {
                                       action: 1, 
                                       uuid: currentStage,
                                       param: data.otherNode.key                                             
                                    },
                                    success: function (data) {
                                        //alert(\'success\');
                                        }
                                    })
                                }
                             else alert (\'Выберите шаблон операции в левом дереве объектов\');                               
			                }'),
                    ],
                    'contextMenu' => [
                        'menu' => [
                            'add' => [
                                'name' => "Добавить",
                                'icon' => "add",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/stage-operation/create");
                                }')
                            ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                     var node = $.ui.fancytree.getNode(opt.$trigger);
                                     $.ajax({
                                        url: "move",
                                        type: "post",
                                        data: {
                                            action: 9, 
                                            uuid: node.key,
                                            param: 0                                            
                                        },
                                        success: function (data) {
                                            node.remove();
                                        }
                                    });
                                }')
                            ],
                            'edit' => [
                                'name' => "Редактировать",
                                'icon' => "edit",
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/stage-operation/update?id="+node.key);                                
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
                    'source' => $operationTemplate,
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
                                    url: "move",
                                    type: "post",
                                    data: {
                                      action: 3, 
                                      uuid: data.node.key,
                                      param: data.node.title                                            
                                    },
                                    success: function (data) {
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
                                     if (node.folder)
                                        var folder = node;
                                     else
                                        var folder = node.parent;                                        
                                     $.ajax({
                                        url: "move",
                                        type: "post",
                                        data: {
                                            action: 4, 
                                            param: folder.key                                         
                                        },
                                        success: function (data) {
                                            if (data>0) {
                                                newNode = folder.addNode({title:\'Новый шаблон\'});
                                                newNode.key = data;
                                            }
                                        }
                                     });                                     
                                    }')
                            ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                     var node = $.ui.fancytree.getNode(opt.$trigger);
                                     $.ajax({
                                        url: "move",
                                        type: "post",
                                        data: {
                                            action: 2, 
                                            uuid: node.key,
                                            param: 0                                            
                                        },
                                        success: function (data) {
                                            if(data == 1) 
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
                                    window.location.replace("/operation-template/update?id="+node.key);
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

