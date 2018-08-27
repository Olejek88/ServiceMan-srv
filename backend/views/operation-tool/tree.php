<?php
/**
 * @var $operationTemplate array
 * @var $operationParts array
 * @var $selectParts array
 * @var $operationTools array
 * @var $selectTools array
 */

$this->title = Yii::t('app', 'Добавление инструментов и ЗИП к операции');

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

?>
<script>
    var currentStage = '0';
</script>

<table style="width: 100%; vertical-align: top; background-color: white" cellpadding="5">
    <tr style="background-color: #3c8dbc; text-align: center; color: white">
    <td>Шаблоны этапов операций</td>
    <td>Выбранный ЗИП</td>
    <td>ЗИП</td>
    </tr>
    <tr style="vertical-align: top">
        <td style="width: 33%" rowspan="3">
            <?php
            if (!$operationTemplate) $operationTemplate=[];
            if (!$selectParts) $selectParts=[];
            if (!$operationParts) $operationParts=[];
            if (!$selectTools) $selectTools=[];
            if (!$operationTools) $operationTools=[];
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $operationTemplate,
                    'extensions' => ['contextMenu','edit'],
                    'activate' => new JsExpression('function(event, data) {
                        currentStage = data.node.key;
                        $.ajax({
                            url: "check-parts",
                            type: "post",
                            data: {
                                uuid: data.node.key                                            
                            }, success: function (data) {
                                var tree = $("#fancyree_w1").fancytree("getTree");                                        
                                tree.reload(JSON.parse(data)).done(function() {
                                    var rootNode = $("#fancyree_w1").fancytree("getTree");
                                    tree.visit(function(node){
                                        node.setExpanded(true);
                                    });
                                });
                            }
                        });
                        $.ajax({
                            url: "check-tools",
                            type: "post",
                            data: {
                                uuid: data.node.key                                            
                            }, success: function (data) {
                                var tree = $("#fancyree_w3").fancytree("getTree");                                        
                                tree.reload(JSON.parse(data)).done(function() {
                                    var rootNode = $("#fancyree_w3").fancytree("getTree");
                                    tree.visit(function(node){
                                        node.setExpanded(true);
                                    });
                                });
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
                                    url: "edit-template",
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
                                    var folder = node.parent;
                                    $.ajax({
                                        url: "add-template",
                                        type: "post",
                                        data: {
                                            param: folder.key                                            
                                        },
                                        success: function (data) {
                                            if (data>0) {
                                                node = folder.addNode({title:\'Новый шаблон\'});
                                                node.key = data;
                                            } else alert (\'Невозможно создать шаблон в этом месте\');
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
                                        url: "delete-template",
                                        type: "post",
                                        data: {
                                            uuid: node.key
                                        },
                                        success: function (data) {
                                            if (data==0)
                                                node.remove();
                                            else
                                                alert(\'Ошибка удаления шаблона\');
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
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $selectParts,
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
                                    url: "move-part",
                                    type: "post",
                                    data: {
                                        uuid: currentStage,
                                        param: data.otherNode.key                                             
                                    },
                                    success: function (data) {
                                        if (data == 0)
                                            data.otherNode.copyTo(node, data.hitMode);
                                        else
                                            alert (\'Невозможно привязать запчасть\');                                         
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
                                    window.location.replace("/operation-part/create");
                                }')
                            ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    $.ajax({
                                        url: "delete-operation-part",
                                        type: "post",
                                        data: {
                                            uuid: node.key,
                                            param: 0                                            
                                        },
                                        success: function (data) {
                                            if (data==0)
                                                node.remove();
                                            else
                                                alert(\'Ошибка удаления ЗИП\');
                                        }
                                    });
                                }')
                            ],
                            'edit' => [
                                'name' => "Редактировать",
                                'icon' => "edit",
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/operation-part/update?id="+node.key);                                
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
                    'source' => $operationParts,
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
                                    url: "edit-part",
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
                                     var folder = node.parent;
                                     var param = folder.key;
                                     if (folder.key.includes("root")) {
                                         param=node.key;
                                        }
                                     $.ajax({
                                        url: "add-part",
                                        type: "post",
                                        data: {
                                            param: param                                         
                                        },
                                        success: function (data) {
                                            if (data > 0) {
                                                if (folder.key.includes("root"))
                                                    var nodes = node.addNode({title:\'Новый ЗиП\'});                                                    
                                                else
                                                    var nodes = folder.addNode({title:\'Новый ЗиП\'});
                                                nodes.key = data;
                                            } else alert (\'Невозможно создать ЗиП в этом месте\');
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
                                        url: "delete-part",
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
                                    window.location.replace("/operation-part/update?id="+node.key);
                                }')
                            ]
                        ]
                    ]
                ]
            ]);
            ?>
        </td>
    </tr>
    <tr style="background-color: #3c8dbc; text-align: center; color: white">
        <td>Выбранный инструмент</td>
        <td>Инструменты</td>
    </tr>
    <tr style="vertical-align: top">
        <td style="width: 33%">
            <?php
            echo FancytreeWidget::widget([
                'options' =>[
                    'source' => $selectTools,
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
                                    url: "move-tool",
                                    type: "post",
                                    data: {
                                        uuid: currentStage,
                                        param: data.otherNode.key                                             
                                    },
                                    success: function (data) { 
                                        if (data == 0)
                                            data.otherNode.copyTo(node, data.hitMode);
                                        else
                                            alert (\'Невозможно привязать инструмент\');
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
                                    window.location.replace("/operation-tool/create");
                                }')
                            ],
                            'delete' => [
                                'name' => "Удалить",
                                'icon' => "delete",
                                'callback' => new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    $.ajax({
                                        url: "delete-operation-tool",
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
                                'name' => "Редактировать",
                                'icon' => "edit",
                                'callback' =>new JsExpression('function(key, opt) {
                                    var node = $.ui.fancytree.getNode(opt.$trigger);
                                    window.location.replace("/operation-part/update?id="+node.key);                                
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
                    'source' => $operationTools,
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
                                     var folder = node.parent;
                                     var param = folder.key;
                                     if (folder.key.includes("root")) {
                                         param=node.key;
                                        }
                                     $.ajax({
                                        url: "add-tool",
                                        type: "post",
                                        data: {
                                            param: param                                         
                                        },
                                        success: function (data) {
                                            if (data > 0) {
                                                if (folder.key.includes("root"))
                                                    var nodes = node.addNode({title:\'Новый инструмент\'});                                                    
                                                else
                                                    var nodes = folder.addNode({title:\'Новый инструмент\'});
                                                nodes.key = data;
                                            } else alert (\'Невозможно создать инструмент в этом месте\');
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
                                        url: "delete-tool",
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
                                    window.location.replace("/operation-part/update?id="+node.key);
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

