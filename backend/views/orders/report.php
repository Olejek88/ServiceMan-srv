<?php

/* @var $this yii\web\View */
/* @var $model */

$this->title = $model['title'];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Наряды'),
    'url' => ['index']
];
?>

<div class="order-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                Отчет сформирован (<a href="<?php echo $file; ?>">открыть отчет</a>)
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                </div>
            </div>

        </div>
    </div>
</div>
