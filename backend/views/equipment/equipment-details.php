<?php

use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\Measure;
use yii\helpers\Html;

/* @var $model \common\models\Equipment */

$equipment = Equipment::find()
    ->where(['uuid' => $model['uuid']])
    ->one();
$models = Equipment::findOne($model['_id']);

$measures = Measure::find()
    ->where(['equipmentUuid' => $equipment['uuid']])
    ->all();

$categories = "'-'";
$values="data: [";
$zero = 0;

$counts=0;
foreach ($measures as $measure) {
    if ($counts > 0) {
        $values .= ",";
    }
    $values .= $measure['value'];
    $categories[$counts]=$measure['date'];
    $values[$counts]=$measure['value'];
    $counts++;
}
$values .= "]}";
?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1"><div class="kv-detail-content">
                <h3><?php echo $equipment['equipmentType']->title ?></h3>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="img-thumbnail img-rounded text-center">
                            <img src="<?php echo Html::encode(MainFunctions::getImagePath('equipment', $equipment['uuid'])) ?>" style="padding:2px;width:100%">
                            <div class="small text-muted"><?php echo $equipment['serial'] ?></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="2" class="text-center text-danger">Последние показания</th>
                            </tr>
                            <?php
                                foreach ($measures as $measure) {
                                    echo '<tr><td>'.$measure['date'].'</td>
                                          <td class="text-right">'.$measure['value'].'</td></tr>';
                                }
                            ?>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="danger">
                                <td class="text-center">
                                    <div id="container" style="height: 250px;"></div>
                                    <script src="/js/vendor/lib/HighCharts/highcharts.js"></script>
                                    <script src="/js/vendor/lib/HighCharts/modules/exporting.js"></script>
                                    <script type="text/javascript">
                                        Highcharts.chart('container', {
                                            data: {
                                                table: 'datatable'
                                            },
                                            chart: {
                                                type: 'column'
                                            },
                                            title: {
                                                text: ''
                                            },
                                            xAxis: {
                                                categories: [
                                                    <?php
                                                        echo $categories;
                                                    ?>
                                                ]
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
                                            series: [
                                                <?php
                                                    echo $values;
                                                ?>
                                            ]
                                        });
                            </script>
                        </div>

                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
