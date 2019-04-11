<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model common\models\Task */
/* @var $stages */
/* @var $operationIndex */
/* @var $operations */
/* @var $order */

$this->title = Yii::t('app', 'Информация по задаче');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Наряд №' . $order['_id']),
    'url' => ['/orders/info?id=' . $order['_id']]
];
?>

<div class="orders-view box-padding">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0 5px;">Информация по задаче</h3>
        <div class="input-group" style="width: 200px; margin: 0 auto;">
            <span class="input-group-addon" id="basic-addon1"
                  style="font-size: 2em; border: 1px solid #fff; color: #333;">№</span>
            <input type="text" class="form-control" value="<?= Html::encode($model->_id) ?>"
                   aria-describedby="basic-addon1" style="font-size: 2em; color: #333;">
        </div>

        <div class="modal model-settings" data-backdrop="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title text-center">Список зависимостей</h4>
                    </div>
                    <div class="modal-body">
                        <div style="color: #666;">
                            <div class="container">
                                <ul class="timeline">
                                    <li>
                                        <div class="timeline-badge" style="left: 15px;"><i
                                                    class="glyphicon glyphicon-check"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h6 style="font-size: 16px;"><b>Наряд</b></h6>
                                            </div>
                                            <div class="timeline-body">
                                                <a href="/orders/<?= Html::encode($order['_id']) ?>">
                                                    <h6>
                                                        <?= Html::encode($order['title']) ?>
                                                    </h6>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-badge" style="left: 15px;"><i
                                                    class="glyphicon glyphicon-check"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h6 style="font-size: 16px;"><b>Задача</b></h6>
                                            </div>
                                            <div class="timeline-body">
                                                <h6>
                                                    <?= Html::encode($model->comment) ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <ul class="nav nav-tabs header-result-panel" style="width: 182px;">
                            <li class="active"><a href="#task-stage" data-toggle="tab">Этапы</a></li>
                            <li><a href="#operation" data-toggle="tab">Операции</a></li>
                        </ul>

                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in box-default-modal" id="task-stage">
                                <?php if (!empty($stages)): ?>
                                    <?php
                                    $countLog = count($stages);
                                    ?>
                                    <table class="table table-striped table-hover text-left">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название</th>
                                            <th>Статус</th>
                                            <th>Вердикт</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($stages as $key => $value): ?>
                                            <tr>
                                                <td><?= Html::encode($key + 1) ?></td>
                                                <td>
                                                    <a href="/task-stage/info?id=<?= Html::encode($value['_id']) ?>">
                                                        <?= Html::encode($value['comment']) ?>
                                                    </a>
                                                </td>
                                                <td style="width: 100px;"><?= Html::encode($value['stageStatusUuid']) ?></td>
                                                <td style="width: 100px;"><?= Html::encode($value['stageVerdictUuid']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h6 class="text-center">Данный раздел находится в разработке..</h6>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane fade box-default-modal" id="operation">
                                <?php if (!empty($operations)): ?>
                                    <?php
                                    $countLog = count($operations);
                                    ?>
                                    <table class="table table-striped table-hover text-left">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название</th>
                                            <th>Статус</th>
                                            <th>Вердикт</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $index = 0; ?>
                                        <?php foreach ($operations as $operation): ?>
                                            <?php foreach ($operation as $key => $value): ?>
                                                <?php $key = $index; ?>
                                                <tr>
                                                    <td><?= Html::encode($index = $key + 1) ?></td>
                                                    <td>
                                                        <a href="/operation/info?id=<?= Html::encode($value['_id']) ?>">
                                                            <?= Html::encode($value['operationTemplateUuid']) ?>
                                                        </a>
                                                    </td>
                                                    <td style="width: 100px;"><?= Html::encode($value['operationStatusUuid']) ?></td>
                                                    <td style="width: 100px;"><?= Html::encode($value['operationVerdictUuid']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h6 class="text-center">Данный раздел находится в разработке..</h6>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel-body">
            <header class="header-result">

                <ul class="nav nav-tabs header-result-panel" style="width: 363px;">
                    <li class="active"><a href="#id" data-toggle="tab">Идентификаторы</a></li>
                    <li class=""><a href="#characteristics" data-toggle="tab">Характеристики</a></li>
                    <li class=""><a href="#dependencies" data-toggle="tab">Зависимости</a></li>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="id" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Идентификаторы</h4>

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'uuid',
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
                                'comment:ntext',
                                'createdAt',
                                'changedAt',
                            ],
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="characteristics" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Характеристики</h4>

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'startDate',
                                'endDate',
                                'prevCode',
                                'nextCode',
                            ],
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="dependencies" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Этапы</h4>

                        <table class="table table-striped" style="border: 1px solid #eee;">
                            <tbody>
                            <tr class="text-center">
                                <td>Количество этапов: <?= Html::encode(count($stages)) ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <h4 class="text-center">Операции</h4>

                        <table class="table table-striped" style="border: 1px solid #eee;">
                            <tbody>
                            <tr class="text-center">
                                <td>Количество операций: <?= Html::encode($operationIndex) ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <h5 class="text-center">
                            <a href="#" class="list-tasks">Показать</a>
                        </h5>

                    </div>

                </div>
            </header>

        </div>
    </div>

</div>
