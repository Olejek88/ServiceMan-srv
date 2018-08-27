<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\commands\MainFunctions;
use common\models\Users;
use yii\helpers\ArrayHelper;
use dosamigos\datetimepicker\DateTimePicker;

use common\models\OrderStatus;
use common\models\OrderVerdict;
use common\models\OrderLevel;

/* @var $model common\models\Orders */

$this->title = Yii::t('app', 'Создание наряда');
?>

<div class="orders-view box-padding" style="padding: 0">

    <div class="modal model-search-author" id="model-search" data-backdrop="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center">Выбор пользователя</h4>
                    <!-- <input type="text" class="form-control" id="order-filtr-input" v-model="input" placeholder="Введите ваш запрос" autofocus> -->
                </div>
                <div class="modal-body">

                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="punkt-1">

                            <?php $form = ActiveForm::begin([
                                    'id' => 'form-input-documentation',
                                    'options' => [
                                        'class' => 'form-horizontal form-orders-author',
                                        'enctype' => 'multipart/form-data'
                                    ],
                                ]);
                            ?>

                            <!-- <h6 class="text-center">Данный раздел находится в разработке..</h6> -->
                            <?php

                                $user  = Users::find();
                                $userActive = $user->where(
                                                        'active != :active',
                                                        ['active' => '0'])
                                                    ->asArray()
                                                    ->all();
                                $params    = [
                                    'prompt' => 'Выберите пользователя...',
                                    'id'     => 'orders-authorUuid'
                                ];

                                $items = ArrayHelper::map($userActive,'uuid','name');

                                echo $form->field($model, 'authorUuid')->dropDownList($items, $params);

                            ?>

                            <?php ActiveForm::end(); ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal model-search-user" id="model-search" data-backdrop="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center">Выбор пользователя</h4>
                    <!-- <input type="text" class="form-control" id="order-filtr-input" v-model="input" placeholder="Введите ваш запрос" autofocus> -->
                </div>
                <div class="modal-body">

                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="punkt-1">

                            <?php $form = ActiveForm::begin([
                                    'id' => 'form-input-documentation',
                                    'options' => [
                                        'class' => 'form-horizontal form-orders-author',
                                        'enctype' => 'multipart/form-data'
                                    ],
                                ]);
                            ?>

                            <?php

                                $user  = Users::find();
                                $userActive = $user->where(
                                                        'active != :active',
                                                        ['active' => '0'])
                                                    ->all();
                                $params    = [
                                    'prompt'    => 'Выберите пользователя...',
                                    'id'        => 'orders-userUuid',
                                ];

                                $items = ArrayHelper::map($userActive,'uuid','name');

                                echo $form->field($model, 'userUuid')->dropDownList($items, $params);

                            ?>

                            <?php ActiveForm::end(); ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0 5px;">Создание рабочих процессов</h3>

        <div class="panel-body">

            <?php $form = ActiveForm::begin([
                    'id' => 'form-input-documentation',
                    'options' => [
                        'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                        'enctype' => 'multipart/form-data'
                    ],
                ]);
            ?>

            <header class="header-result">

                <ul class="nav nav-tabs" style="width: 348px; margin: 0 auto;">
                    <li class="active"><?php echo Html::a('Наряд', ['/orders/generate']); ?></li>
                    <li class=""><?php echo Html::a('Задача', ['/task/generate']); ?></li>
                    <li class=""><?php echo Html::a('Этап', ['/stage/generate']); ?></li>
                    <li class=""><?php echo Html::a('Операция', ['/operation/generate']); ?></li>
                </ul>


                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="today">
                        <h6>
                            <!-- Данный раздел находится в разработке.. -->

                            <?php

                                $model->load(Yii::$app->request->post());

                                if (!$model->isNewRecord) {
                                    echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
                                } else {
                                    echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
                                }

                            ?>

                            <?= $form->field($model, 'title')->textInput(['placeholder' => 'Введите название..', 'maxlength' => true, 'id' => 'title']) ?>
                            <?= $form->field($model, 'comment')->textInput(['placeholder' => 'Введите название..', 'maxlength' => true, 'id' => 'title']) ?>
                            <?= $form->field($model, 'reason')->textInput(['placeholder' => 'Введите название..', 'maxlength' => true, 'id' => 'title']) ?>

                            <?php

                                $user  = Users::find();
                                $userActive = $user->where(
                                                        'active != :active',
                                                        ['active' => '0'])
                                                    ->all();
                                $params    = [
                                    'prompt'    => 'Выберите пользователя...',
                                    'id'        => 'authorUuid',
                                ];

                                $items = ArrayHelper::map($userActive,'uuid','name');

                                echo $form->field($model, 'authorUuid')->dropDownList($items, $params);

                            ?>


                            <?php

                                $user  = Users::find();
                                $userActive = $user->where(
                                                        'active != :active',
                                                        ['active' => '0'])
                                                    ->all();
                                $params    = [
                                    'prompt'    => 'Выберите пользователя...',
                                    'id'        => 'userUuid',
                                ];

                                $items = ArrayHelper::map($userActive,'uuid','name');

                                echo $form->field($model, 'userUuid')->dropDownList($items, $params);

                            ?>

                            <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
                            <p style="width: 0; margin-bottom: 0;">Дата</p>
                                <?= DateTimePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'startDate',
                                    'language' => 'ru',
                                    'size' => 'ms',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                                        'todayBtn' => true
                                    ]
                                    ]);
                                ?>
                            </div>

                            <?php

                                $orderStatus = OrderStatus::find()->all();
                                $items       = ArrayHelper::map($orderStatus,'uuid','title');

                                echo $form->field($model, 'orderStatusUuid')->dropDownList($items);

                            ?>

                            <?php

                                $orderVerdict = OrderVerdict::find()->all();
                                $items        = ArrayHelper::map($orderVerdict,'uuid','title');

                                echo $form->field($model, 'orderVerdictUuid')->dropDownList($items);

                            ?>

                            <?php

                                $orderLevel = OrderLevel::find()->all();
                                $items      = ArrayHelper::map($orderLevel,'uuid','title');

                                echo $form->field($model, 'orderLevelUuid')->dropDownList($items);

                            ?>
                        </h6>
                    </div>
                </div>

                <div class="form-group text-center">

                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
                        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
                    ]) ?>

                </div>

                <h6 class='text-center'>
                    * Если вы не нашли необходимую вам категорию, вы всегда можете создать её <b><?= Html::a('сами!', ['/orders'], ['target' => '_blank',]) ?></b>
                </h6>

            </header>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
