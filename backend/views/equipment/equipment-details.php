<?php

use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\Measure;
use common\models\Photo;
use yii\helpers\Html;

/* @var $model Equipment */

$equipment = Equipment::find()
    ->where(['uuid' => $model['uuid']])
    ->one();
$models = Equipment::findOne($model['_id']);

$measures = Measure::find()
    ->where(['equipmentUuid' => $equipment['uuid']])
    ->all();
$photo = Photo::find()
    ->where(['objectUuid' => $equipment['uuid']])
    ->orderBy('createdAt DESC')
    ->one();
$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');

$categories = "[";
$values = "name: 'Значения', data: [";
$zero = 0;
$num = rand(0, 1000);
$counts = 0;
foreach ($measures as $measure) {
    if ($counts > 0) {
        $values .= ",";
        $categories .= ",";
    }
    $values .= $measure['value'];
    $categories .= "'" . date_format(date_create($measure['date']), 'Y-m-d') . "'";
    $counts++;
}
$values .= "]";
$categories .= "]";
?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1">
            <div class="kv-detail-content">
                <h3><?php echo $equipment->title ?></h3>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="img-thumbnail img-rounded text-center">
                            <?php
                            if ($photo != null) {
                                echo '<img src="' .
                                    Html::encode(MainFunctions::getImagePath('equipment', $photo['uuid'])) . '
                                        " style="padding:2px;width:100%">';
                            }
                            ?>
                            <div class="small text-muted"><?php echo $equipment['serial'] ?></div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="2" class="text-center text-danger">Последние показания</th>
                            </tr>
                            <?php
                            foreach ($measures as $measure) {
                                echo '<tr><td>' . $measure['date'] . '</td>
                                          <td class="text-right">' . $measure['value'] . '</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="danger">
                                <td class="text-center">
                                    <div id="container<?php echo $num ?>" style="height: 250px; width: 430px"></div>
                                    <script src="/js/vendor/lib/HighCharts/highcharts.js"></script>
                                    <script src="/js/vendor/lib/HighCharts/modules/exporting.js"></script>
                                    <script type="text/javascript">
                                        Highcharts.chart('container<?php echo $num ?>', {
                                            data: {
                                                table: 'data_table'
                                            },
                                            chart: {
                                                type: 'column'
                                            },
                                            title: {
                                                text: ''
                                            },
                                            legend: {
                                                enabled: false
                                            },
                                            xAxis: {
                                                categories:
                                                <?php
                                                echo $categories;
                                                ?>
                                            },
                                            tooltip: {
                                                headerFormat: '<b>{point.x}</b><br/>',
                                                pointFormat: '{series.name}: {point.y}'
                                            },
                                            plotOptions: {
                                                column: {
                                                    stacking: 'normal',
                                                    dataLabels: {
                                                        enabled: true,
                                                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                                    }
                                                }
                                            },
                                            yAxis: {
                                                min: 0,
                                                title: {
                                                    text: 'Последние данные'
                                                }
                                            },
                                            series: [{
                                                <?php
                                                echo $values;
                                                ?>
                                            }]
                                        });
                                    </script>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-3">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="2" class="text-center text-info">Информация</th>
                            </tr>
                            <tr><td>Дата ввода в эксплуатацию</td><td class="text-right"><?php echo date ('d-m-Y',strtotime($equipment['inputDate'])) ?></td></tr>
                            <tr><td>Дата поверки</td><td class="text-right"><?php echo date ('d-m-Y',strtotime($equipment['testDate'])) ?></td></tr>
                            <tr><td>Дата следущей поверки</td><td class="text-right"><?php echo date ('d-m-Y',strtotime($equipment['nextDate'])) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
