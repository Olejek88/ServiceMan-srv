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


/* @var $this yii\web\View */
/* @var $model common\models\Orders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orders-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php

        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'value' => (new MainFunctions)->GUID()]);
        }

    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

    <?php

        $user  = Users::find()->all();
        $items = ArrayHelper::map($user,'uuid','name');

        echo $form->field($model, 'authorUuid')->dropDownList($items);

    ?>

    <?php

        $user  = Users::find()->all();
        $items = ArrayHelper::map($user,'uuid','name');
        echo $form->field($model, 'userUuid')->dropDownList($items);

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

    <?= $form->field($model, 'attemptCount')->textInput(['value' => 0, 'readonly' => true]) ?>

    <?= $form->field($model, 'updated')->textInput(['value' => 0, 'readonly' => true]) ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
