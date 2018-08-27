<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;


$this->title = Yii::t('app', 'Информация по этапу');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Задача №'.$task['_id']), 'url' => ['/task/info?id='.$task['_id']]];
?>

<div class="orders-view box-padding">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0px 5px;">Информация по этапу</h3>
        <div class="input-group" style="width: 200px; margin: 0 auto;">
            <span class="input-group-addon" id="basic-addon1" style="font-size: 2em; border: 1px solid #fff; color: #333;">№</span>
            <input type="text" class="form-control" value="<?= Html::encode($model->_id) ?>" aria-describedby="basic-addon1" style="font-size: 2em; color: #333;">
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
                                      <div class="timeline-badge" style="left: 15px;"><i class="glyphicon glyphicon-check"></i></div>
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
                                      <div class="timeline-badge" style="left: 15px;"><i class="glyphicon glyphicon-check"></i></div>
                                      <div class="timeline-panel">
                                        <div class="timeline-heading">
                                          <h6 style="font-size: 16px;"><b>Задача</b></h6>
                                        </div>
                                        <div class="timeline-body">
                                            <a href="/task/<?= Html::encode($task['_id']) ?>">
                                                <h6>
                                                    <?= Html::encode($task['comment']) ?>
                                                </h6>
                                            </a>
                                        </div>
                                      </div>
                                    </li>
                                    <li>
                                      <div class="timeline-badge" style="left: 15px;"><i class="glyphicon glyphicon-check"></i></div>
                                      <div class="timeline-panel">
                                        <div class="timeline-heading">
                                          <h6 style="font-size: 16px;"><b>Этап</b></h6>
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

                        <ul class="nav nav-tabs header-result-panel" style="width: 80px;">
                            <li class="active"><a href="#operation" data-toggle="tab">Операции</a></li>
                        </ul>

                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="task-stage" style="height: 150px; overflow-y: auto;">
                                <?php if (!empty($operations)): ?>
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
                                            <?php foreach ($operations as $key => $value): ?>
                                                <tr>
                                                    <td><?= Html::encode($key + 1) ?></td>
                                                    <td>
                                                        <a href="/operation/info?id=<?= Html::encode($value['_id']) ?>">
                                                            <?= Html::encode($value['operationTemplateUuid']) ?>
                                                        </a>
                                                    </td>
                                                    <td style="width: 100px;"><?= Html::encode($value['operationStatusUuid']) ?></td>
                                                    <td style="width: 100px;"><?= Html::encode($value['operationVerdictUuid']) ?></td>
                                                </tr>
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
                                [
                                    'label' => 'Uuid',
                                    'value' => $model->uuid
                                ],
                                [
                                    'label' => 'Шаблон',
                                    'value' => $template['title']
                                ],
                                [
                                    'label' => 'Статус',
                                    'value' => $status['title']
                                ],
                                [
                                    'label' => 'Вердикт',
                                    'value' => $verdict['title']
                                ],
                                [
                                    'label' => 'Комментарий',
                                    'value' => $model->comment
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
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="characteristics" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Характеристики</h4>

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'label' => 'Задача',
                                    'value' => $task['comment']
                                ],
                                [
                                    'label' => 'Оборудование',
                                    'value' => $equipment['title']
                                ],
                            ],
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="dependencies" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Операции</h4>

                        <table class="table table-striped" style="border: 1px solid #eee;">
                            <tbody>
                                <tr class="text-center">
                                    <td>Количество операций: <?= Html::encode(count($operations)) ?></td>
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
