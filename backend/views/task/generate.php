<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\commands\MainFunctions;
use common\models\Orders;
use common\models\Equipment;
use common\models\TaskVerdict;
use common\models\WorkStatus;
use common\models\TaskTemplate;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = Yii::t('app', 'Создание задачи');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Задача'), 'url' => ['index']];
?>

<div class="orders-view box-padding" style="padding: 0px;">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0px 5px;">Создание рабочих процессов</h3>

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

                <ul class="nav nav-tabs" style="width: 318px; margin: 0 auto;">
                    <li class=""><a href="/orders/generate">Наряд</a></li>
                    <li class="active"><a href="/task/generate">Задача</a></li>
                    <li class=""><a href="/stage/generate">Этап</a></li>
                    <li class=""><a href="/operation/generate">Операция</a></li>
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

                            <?php

                                $ordersAll    = Orders::find();
                                $ordersActive = $ordersAll
                                                        ->where(
                                                            'orderStatusUuid != :orderStatusUuid',
                                                            ['orderStatusUuid' => '53238221-0EF7-4737-975E-FD49AFC92A05'])
                                                        ->all();

                                $items = ArrayHelper::map($ordersActive, 'uuid', 'title');

                                echo $form->field($model, 'orderUuid')->dropDownList($items);

                            ?>

                            <?php

                                $taskstatus = WorkStatus::find()->all();
                                $items      = ArrayHelper::map($taskstatus,'uuid','title');

                                echo $form->field($model, 'taskStatusUuid')->dropDownList($items);

                            ?>

                            <?php

                                $taskverdict = TaskVerdict::find()->all();
                                $items       = ArrayHelper::map($taskverdict,'uuid','title');

                                echo $form->field($model, 'taskVerdictUuid')->dropDownList($items);

                            ?>

                            <?php

                                $tasktemplate = TaskTemplate::find()->all();
                                $items = ArrayHelper::map($tasktemplate,'uuid','title');

                                echo $form->field($model, 'taskTemplateUuid')->dropDownList($items);

                            ?>

                            <?= $form->field($model, 'comment')->textarea(['rows' => 3, 'style' => 'resize: none;']) ?>
                        </h6>
                    </div>
                </div>

                <div class="form-group text-center">

                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
                        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
                    ]) ?>

                </div>

                <h6 class='text-center'>
                    * Если вы не нашли необходимую вам категорию, вы всегда можете создать её <b><?= Html::a('сами!', ['/task'], ['target' => '_blank',]) ?></b>
                </h6>

            </header>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
