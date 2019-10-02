<?php

use backend\models\AccessModel;
use backend\models\AccessSearch;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $searchModel AccessSearch */
?>
    <h1>Настройка прав доступа к разделам</h1>

<?php
$this->title = Yii::t('app', 'Настройка прав доступа');

$form = ActiveForm::begin(
    [
        'id' => 'form-input-access',
        'options' => [
        ],
    ]
);
?>

<?php
try {
    echo GridView::widget([
            'dataProvider' => $dataProvider,
//            'pjax' => true,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'visible' => false,
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'description',
                    'header' => 'Раздел',
                    'group' => true,
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'permission',
                    'header' => 'Разрешение',
                    'group' => true,
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                    'header' => 'Администратор',
                    'content' => function ($model) {
                        /** @var AccessModel $model */
                        $aa = $model->permission . $model->model . 'Admin';
                        $idx = $model->permission . $model->model;
                        $oldValue = $model->admin === true ? 1 : 0;
                        $outHtml = Html::checkbox('adminCb[]', $oldValue === 1, [
                            'value' => $idx,
                            'data-store' => $aa,
                            'data-old' => $oldValue,
                            'class' => ['work-cb'],
                        ]);
                        $outHtml .= Html::hiddenInput('admin[' . $idx . '][value]', $oldValue, ['id' => $aa,]);
                        $outHtml .= Html::hiddenInput('admin[' . $idx . '][ch]', 0, ['id' => $aa . 'Ch',]);
                        return $outHtml;
                    },
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => 'Оператор',
                    'content' => function ($model) {
                        /** @var AccessModel $model */
                        $aa = $model->permission . $model->model . 'Oper';
                        $idx = $model->permission . $model->model;
                        $oldValue = $model->operator === true ? 1 : 0;
                        $outHtml = Html::checkbox('operCb[]', $oldValue === 1, [
                            'value' => $idx,
                            'data-store' => $aa,
                            'data-old' => $oldValue,
                            'class' => ['work-cb'],
                        ]);
                        $outHtml .= Html::hiddenInput('oper[' . $idx . '][value]', $oldValue, ['id' => $aa,]);
                        $outHtml .= Html::hiddenInput('oper[' . $idx . '][ch]', 0, ['id' => $aa . 'Ch',]);
                        return $outHtml;
                    },
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => 'Диспетчер',
                    'content' => function ($model) {
                        /** @var AccessModel $model */
                        $aa = $model->permission . $model->model . 'Disp';
                        $idx = $model->permission . $model->model;
                        $oldValue = $model->dispatch === true ? 1 : 0;
                        $outHtml = Html::checkbox('dispCb[]', $oldValue === 1, [
                            'value' => $idx,
                            'data-store' => $aa,
                            'data-old' => $oldValue,
                            'class' => ['work-cb'],
                        ]);
                        $outHtml .= Html::hiddenInput('disp[' . $idx . '][value]', $oldValue, ['id' => $aa,]);
                        $outHtml .= Html::hiddenInput('disp[' . $idx . '][ch]', 0, ['id' => $aa . 'Ch',]);
                        return $outHtml;
                    },
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => 'Директор',
                    'content' => function ($model) {
                        /** @var AccessModel $model */
                        $aa = $model->permission . $model->model . 'Dir';
                        $idx = $model->permission . $model->model;
                        $oldValue = $model->director === true ? 1 : 0;
                        $outHtml = Html::checkbox('dirCb[]', $oldValue === 1, [
                            'value' => $idx,
                            'data-store' => $aa,
                            'data-old' => $oldValue,
                            'class' => ['work-cb'],
                        ]);
                        $outHtml .= Html::hiddenInput('dir[' . $idx . '][value]', $oldValue, ['id' => $aa,]);
                        $outHtml .= Html::hiddenInput('dir[' . $idx . '][ch]', 0, ['id' => $aa . 'Ch',]);
                        return $outHtml;
                    },
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
            ],
        ]
    );

} catch (Exception $e) {
    ?>
    <p><?php echo $e->getMessage() ?></p>
    <?php
}

$js = <<< SCRIPT
$("#form-input-access").attr("action", "/access/update");
$(this).submit();
SCRIPT;

echo Html::submitButton('Сохранить', [
    'class' => 'btn btn-primary',
    'onClick' => $js,
]);

//$this->registerJs('
//$(".work-cb").on("change", function(){
//var currentValue = $(this).is(":checked")?1:0;
//var storeId = $(this).data("store");
//var changedId = storeId + "Ch";
//$("#" + storeId).val(currentValue);
//$("#" + changedId).val(currentValue == $(this).data("old") ? 0 : 1);
//});
//');

$js = <<< SCRIPT
function assistFunction() {
    $(".work-cb").on("change", function(){
        var currentValue = $(this).is(":checked")?1:0;
        var storeId = $(this).data("store");
        var changedId = storeId + "Ch";
        $("#" + storeId).val(currentValue);
        $("#" + changedId).val(currentValue == $(this).data("old") ? 0 : 1);
    });
};
$(assistFunction);
$(document).on('pjax:end', assistFunction);
SCRIPT;
$this->registerJs($js);
ActiveForm::end();

?>