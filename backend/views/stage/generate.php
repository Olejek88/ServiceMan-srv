<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\Task;
use common\models\StageStatus;
use common\models\StageVerdict;
use common\models\StageTemplate;
use yii\db\Query;
use yii\base\DynamicModel;

$this->title = Yii::t('app', 'Создание этапа');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Этап'),
    'url' => ['index']
];
?>

<div class="orders-view box-padding" style="padding: 0;">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0 5px;">Создание рабочих процессов</h3>

        <div class="panel-body">

            <?php $form = ActiveForm::begin(
                [
                    'id' => 'form-input-documentation',
                    'options' => [
                        'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                        'enctype' => 'multipart/form-data'
                    ],
                ]
            );
            ?>

            <header class="header-result">

                <ul class="nav nav-tabs" style="width: 418px; margin: 0 auto;">
                    <li class=""><a href="/orders/generate">Наряд</a></li>
                    <li class=""><a href="/task/generate">Задача</a></li>
                    <li class="active"><a href="/stage/generate">Этап</a></li>
                    <li class=""><a href="/operation/generate">Операция</a></li>
                </ul>


                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="today">
                        <h6>
                            <!-- Данный раздел находится в разработке.. -->

                            <?php
                            $model->load(Yii::$app->request->post());
                            if (!$model->isNewRecord) {
                                echo $form->field($model, 'uuid')
                                    ->textInput(
                                        ['maxlength' => true, 'readonly' => true]
                                    );
                            } else {
                                echo $form->field($model, 'uuid')
                                    ->textInput(
                                        [
                                            'maxlength' => true,
                                            'value' => (new MainFunctions)->GUID()
                                        ]
                                    );
                            }

                            $rows = Task::find()->all();
                            $items = ArrayHelper::map($rows, 'uuid', 'taskFullName');
                            echo $form->field($model, 'taskUuid')
                                ->dropDownList($items);

                            $rows = StageStatus::find()->all();
                            $items = ArrayHelper::map($rows, 'uuid', 'title');
                            echo $form->field($model, 'stageStatusUuid')
                                ->dropDownList($items);

                            $rows = StageVerdict::find()->all();
                            $items = ArrayHelper::map($rows, 'uuid', 'title');
                            echo $form->field($model, 'stageVerdictUuid')
                                ->dropDownList($items);

                            $query = new Query();
                            $query->select('eq.uuid as eUuid, eq.title as eTitle, st.uuid as sUuid, st.title as sTitle')
                                ->from('equipment_stage as es')
                                ->leftJoin('stage_operation as so', 'es.stageOperationUuid = so.uuid')
                                ->leftJoin('equipment as eq', 'es.equipmentUuid = eq.uuid')
                                ->leftJoin('stage_template as st', 'so.stageTemplateUuid = st.uuid')
                                ->groupBy(['eq.uuid', 'eq.title', 'st.uuid', 'st.title']);
                            $rows = $query->all();
                            $items = [];
                            foreach ($rows as $row) {
                                $id = $row['eUuid'] . ':' . $row['sUuid'];
                                $items[$row['eTitle']][$id] = $row['sTitle'];
                            }

                            $eqStName = 'eqstage';
                            $entityType = new DynamicModel([$eqStName]);
                            $opt = [
                                'inline' => true,
                                'onchange' => "
                                var id = $(':selected', this).val().split(':');
                                $('#stage-stagetemplateuuid').val(id[1]);
                                $('#stage-equipmentuuid').val(id[0]);
        "
                            ];
                            echo $form->field($entityType, $eqStName)
                                ->dropDownList($items, $opt)
                                ->label('Оборудование/шаблон');
                            $this->registerJs(
                                "
                                jQuery(document).ready(function() {
                                    $('#dynamicmodel-" . $eqStName . "').change();
                                });
                                "
                            );

                            $rows = StageTemplate::find()->all();
                            $items = ArrayHelper::map($rows, 'uuid', 'title');
                            echo $form->field($model, 'stageTemplateUuid')
                                ->dropDownList($items, ['readonly' => true]);

                            $rows = Equipment::find()->all();
                            $items = ArrayHelper::map($rows, 'uuid', 'title');
                            echo $form->field($model, 'equipmentUuid')
                                ->dropDownList($items, ['readonly' => true]);

                            echo $form->field($model, 'comment')->textarea(
                                ['rows' => 3, 'style' => 'resize: none;']
                            ) ?>
                        </h6>
                    </div>
                </div>

                <div class="form-group text-center">

                    <?php
                    echo Html::submitButton(
                        $model->isNewRecord
                            ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'),
                        [
                            'class' => $model->isNewRecord
                                ? 'btn btn-success' : 'btn btn-primary'
                        ]
                    ) ?>

                </div>

                <h6 class='text-center'>
                    * Если вы не нашли необходимую вам категорию, вы всегда можете создать её
                    <b>
                        <?php
                        echo Html::a('сами!', ['/stage'], ['target' => '_blank',])
                        ?>
                    </b>
                </h6>

            </header>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
