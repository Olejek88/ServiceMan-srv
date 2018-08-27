<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\commands\MainFunctions;
use yii\helpers\ArrayHelper;

use common\models\OperationVerdict;
use common\models\OperationStatus;
use common\models\OperationTemplate;
use common\models\Stage;

$this->title = Yii::t('app', 'Создание операции');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Операция'), 'url' => ['index']];
?>

<div class="orders-view box-padding" style="padding: 0;">

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

                <ul class="nav nav-tabs" style="width: 418px; margin: 0 auto;">
                    <li class=""><a href="/orders/generate">Наряд</a></li>
                    <li class=""><a href="/task/generate">Задача</a></li>
                    <li class=""><a href="/stage/generate">Этап</a></li>
                    <li class="active"><a href="/operation/generate">Операция</a></li>
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

                            $stages = Stage::find()
                                ->where(
                                    'stageStatusUuid != :stageStatusUuid',
                                    ['stageStatusUuid' => 'F5C3788B-6659-409F-913F-32555CE327C8'])
                                ->all();

                            $items = ArrayHelper::map($stages, 'uuid', 'stageFullName', 'equipment.title');
                            $params = [
                                'prompt' => 'Выберите этап..',
                            ];

                            echo $form->field($model, 'stageUuid')->dropDownList($items, $params);

                            ?>

                            <?php

                            $operationstatus = OperationStatus::find()->all();
                            $items = ArrayHelper::map($operationstatus, 'uuid', 'title');

                            echo $form->field($model, 'operationStatusUuid')->dropDownList($items);

                            ?>

                            <?php

                            $operationverdict = OperationVerdict::find()->all();
                            $items = ArrayHelper::map($operationverdict, 'uuid', 'title');

                            echo $form->field($model, 'operationVerdictUuid')->dropDownList($items);

                            ?>

                            <?php

                            $operationtemplate = OperationTemplate::find()->all();
                            $items = ArrayHelper::map($operationtemplate, 'uuid', 'title');

                            echo $form->field($model, 'operationTemplateUuid')->dropDownList($items);

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
                    * Если вы не нашли необходимую вам категорию, вы всегда можете создать её
                    <b><?= Html::a('сами!', ['/operation'], ['target' => '_blank',]) ?></b>
                </h6>

            </header>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
