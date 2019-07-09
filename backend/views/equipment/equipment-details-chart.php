<?php
/* @var $equipmentUuid */

use common\models\Measure;

$measures = Measure::find()
    ->where(['equipmentUuid' => $equipmentUuid])
    ->all();

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
