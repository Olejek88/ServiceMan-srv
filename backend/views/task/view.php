<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $template common\models\TaskTemplate */
/* @var $status common\models\TaskStatus */
/* @var $verdict common\models\TaskVerdict */
/* @var $taskTree */

$this->title = $model['taskTemplate']->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Задачи'), 'url' => ['index']];
?>
<div class="task-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <p class="text-center">
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </p>
                    <h6>
                        <?php echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                '_id',
                                'uuid',
                                'comment:ntext',
                                [
                                    'label' => 'Наряд',
                                    'value' => $model['order']->title
                                ],
                                [
                                    'label' => 'Шаблон',
                                    'value' => $model['taskTemplate']->title
                                ],
                                [
                                    'label' => 'Статус',
                                    'value' => $model['taskStatus']->title
                                ],
                                [
                                    'label' => 'Вердикт',
                                    'value' => $model['taskVerdict']->title
                                ],
                                'startDate',
                                'endDate',
                                'prevCode',
                                'nextCode',
                                'createdAt',
                                'changedAt',
                            ],
                        ]) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode("Созданы этапы задач и операции") ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <?php
                    echo FancytreeWidget::widget([
                        'options' =>[
                            'source' => $taskTree,
                            'extensions' => ['contextMenu','edit'],
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
                </div>
            </div>
        </div>
    </div>
</div>
