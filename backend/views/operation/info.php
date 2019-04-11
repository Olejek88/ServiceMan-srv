<?php
/* @var $stage common\models\Task */
/* @var $model common\models\Operation */
/* @var $template common\models\OperationTemplate */
/* @var $status common\models\WorkStatus */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = Yii::t('app', 'Информация по операции');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Задача №' . $model->task['_id']),
    'url' => ['/task/info?id=' . $model->task['_id']]];
?>

<div class="orders-view box-padding">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0 5px;">Информация по операции</h3>
        <div class="input-group" style="width: 200px; margin: 0 auto;">
            <span class="input-group-addon" id="basic-addon1" style="font-size: 2em; border: 1px solid #fff; color: #333;">№</span>
            <span class="input-group-addon" id="basic-addon1"
                  style="font-size: 2em; border: 1px solid #fff; color: #333;">№</span>
            <input type="text" class="form-control" value="<?php echo Html::encode($model->_id) ?>"
                   aria-describedby="basic-addon1" style="font-size: 2em; color: #333;">
        </div>

        <div class="panel-body">
            <header class="header-result">

                <ul class="nav nav-tabs header-result-panel" style="width: 257px;">
                    <li class="active"><a href="#id" data-toggle="tab">Идентификаторы</a></li>
                    <li class=""><a href="#characteristics" data-toggle="tab">Характеристики</a></li>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="id" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Идентификаторы</h4>

                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'label' => 'Uuid',
                                        'value' => $model->uuid
                                    ],
                                    [
                                        'label' => 'Шаблон',
                                        'value' => $model->operationTemplate['title']
                                    ],
                                    [
                                        'label' => 'Статус',
                                        'value' => $model->workStatus['title']
                                    ],
                                    [
                                        'label' => 'Создан',
                                        'value' => $model->createdAt
                                    ],
                                    [
                                        'label' => 'Изменен',
                                        'value' => $model->changedAt
                                    ],
                                ],
                            ]
                        ) ?>

                    </div>

                    <div class="tab-pane fade" id="characteristics" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Характеристики</h4>

                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'label' => 'Задача',
                                        'value' => $model->task['comment']
                                    ],
                                ],
                            ]
                        ) ?>

                    </div>

                </div>
            </header>

        </div>
    </div>

</div>
