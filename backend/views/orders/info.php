<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Orders */
/* @var $author  */
/* @var $author  */
/* @var $status common\models\OrderStatus */
/* @var $verdict common\models\OrderVerdict */
/* @var $level common\models\OrderLevel */
/* @var $tasks array  */
/* @var $stageIndex array */
/* @var $operationIndex common\models\Operation */


$this->title = Yii::t('app', 'Создать наряд');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Наряды'), 'url' => ['index']];
?>

<div class="orders-view box-padding">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0 5px;">Информация по наряду</h3>
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

                        <div style="padding: 10px 10px 0 40px; color: #666;">
                            <h6 style="font-size: 16px;"><b>Наряд</b>: <?= Html::encode($model->title) ?></h6>
                            <!-- <h6 style="font-size: 16px;"><b>Автор</b>: <?= Html::encode($author['name']) ?></h6>
                            <h6 style="font-size: 16px;"><b>Исполнитель</b>: <?= Html::encode($user['name']) ?></h6> -->
                            <hr align="center" width="500" size="2" />
                        </div>

                        <ul class="nav nav-tabs header-result-panel" style="width: 254px;">
                            <li class="active"><a href="#common" data-toggle="tab">Задачи</a></li>
                            <li><a href="#secuire" data-toggle="tab">Этапы</a></li>
                            <li><a href="#history_active" data-toggle="tab">Операции</a></li>
                        </ul>

                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in box-default-modal" id="common">
                                <?php if (!empty($tasks)): ?>
                                    <?php
                                        $countLog = count($tasks);
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
                                            <?php foreach ($tasks as $key => $value): ?>
                                                <tr>
                                                    <td><?= Html::encode($key + 1) ?></td>
                                                    <td>
                                                        <?php echo Html::a(Html::encode($value['comment']),
                                                            ['/task/info', 'id' => Html::encode($value['_id'])]); ?>
                                                    </td>
                                                    <td style="width: 100px;"><?= Html::encode($value['taskStatusUuid']) ?></td>
                                                    <td style="width: 100px;"><?= Html::encode($value['taskVerdictUuid']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h6 class="text-center">Данный раздел находится в разработке..</h6>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane fade box-default-modal" id="secuire">
                                <?php if (!empty($stages)): ?>
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
                                            <?php foreach ($stages as $stage): ?>
                                                <?php foreach ($stage as $key => $value): ?>
                                                    <?php $key = $index; ?>
                                                    <tr>
                                                        <td><?= Html::encode($index = $key + 1) ?></td>
                                                        <td>
                                                            <?php echo Html::a(Html::encode($value['comment']),
                                                                ['/stage/info', 'id' => Html::encode($value['_id'])]); ?>
                                                        </td>
                                                        <td style="width: 100px;"><?= Html::encode($value['stageStatusUuid']) ?></td>
                                                        <td style="width: 100px;"><?= Html::encode($value['stageVerdictUuid']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h6 class="text-center">Данный раздел находится в разработке..</h6>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane fade" id="history_active">
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
                                                            <?php echo Html::a(Html::encode($value['operationTemplateUuid']),
                                                                ['/operation/info', 'id' => Html::encode($value['_id'])]); ?>
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

                <p class="text-center">
                    <?= Html::a(Yii::t('app', 'Обновить'), ['update', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Вы действительно хотите удалить данный элемент?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a(Yii::t('app', 'Наряд'), ['order', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Отчет'), ['report', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
                </p>

                <ul class="nav nav-tabs header-result-panel" style="width: 500px;">
                    <li class="active"><a href="#id" data-toggle="tab">Идентификаторы</a></li>
                    <li class=""><a href="#users" data-toggle="tab">Пользователи</a></li>
                    <li class=""><a href="#characteristics" data-toggle="tab">Характеристики</a></li>
                    <li class=""><a href="#dependencies" data-toggle="tab">Зависимости</a></li>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="id" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Общие данные</h4>

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'uuid',
                                'title',
                                [
                                    'label' => 'Статус',
                                    'value' => $status['title']
                                ],
                                [
                                    'label' => 'Вердикт',
                                    'value' => $verdict['title']
                                ],
                                [
                                    'label' => 'Уровень',
                                    'value' => $level['title']
                                ],
                                'createdAt',
                                'changedAt',
                            ],
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="users" style="width: 500px; margin: 0 auto;">
                        <h4 class="text-center">Пользователи</h4>

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'label' => 'Автор',
                                    'value' => $author['name']
                                ],
                                [
                                    'label' => 'Исполнитель',
                                    'value' => $user['name']
                                ],
                                [
                                    'label' => 'Количество',
                                    'value' => count($user)
                                ],
                            ],
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="characteristics" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Клиент</h4>

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'receivDate',
                                'startDate',
                                'openDate',
                                'closeDate',
                                'attemptSendDate',
                                'attemptCount',
                                'updated',
                            ],
                        ]) ?>

                    </div>

                    <div class="tab-pane fade" id="dependencies" style="width: 500px; margin: 0 auto;">

                        <h4 class="text-center">Задачи</h4>

                        <table class="table table-striped" style="border: 1px solid #eee;">
                            <tbody>
                                <tr class="text-center">
                                    <td>Количество задач: <?= Html::encode(count($tasks)) ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="text-center">Этапы</h4>

                        <table class="table table-striped" style="border: 1px solid #eee;">
                            <tbody>
                                <tr class="text-center">
                                    <td>Количество этапов: <?= Html::encode(count($stageIndex)) ?></td>
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
