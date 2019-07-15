<?php
use backend\assets\AdminLteAsset;

AdminLteAsset::register($this);

/* @var $defectsByModel common\models\Defect */
/* @var $defectsByType common\models\Defect */

$this->title = Yii::t('app', 'Распределение дефектов по типам');
?>

<div class="measured-value-index" style="height: 700px">
    <div id="container" style="min-width:500px; width:49%; height:90%; float:left"></div>
    <div id="container2" style="min-width:500px; width:49%; height:90%; float:right"></div>
</div>

<script src="/js/vendor/lib/HighCharts/highcharts.js"></script>
<script src="/js/vendor/lib/HighCharts/modules/exporting.js"></script>

<script type="text/javascript">
// Build the chart
Highcharts.chart('container', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Распределение дефектов по типам элементов'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            },
            showInLegend: true
        }
    },
    series: [{
        <?php
        $first = 0;
        $bar = "name: 'Типы',";
        $bar .= "colorByPoint: true,";
        $bar .= "data: [";
        foreach ($defectsByType as $defect) {
            if ($first > 0)
                $bar .= "," . PHP_EOL;
            $bar .= '{';
            $bar .= 'name: \'' . $defect['title'] . '\',';
            $bar .= 'y: ' . $defect['cnt'];
            if ($first == 0)
                $bar .= ",sliced: true, selected: true" . PHP_EOL;
            $bar .= '}';
            $first++;
        }
        $bar .= "]}]";
        echo $bar;
        ?>
});
</script>
<script type="text/javascript">
    // Build the chart
    Highcharts.chart('container2', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Распределение дефектов по моделям оборудования'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            <?php
            $first = 0;
            $bar = "name: 'Модели',";
            $bar .= "colorByPoint: true,";
            $bar .= "data: [";
            foreach ($defectsByModel as $defect) {
                if ($first > 0)
                    $bar .= "," . PHP_EOL;
                $bar .= '{';
                $bar .= 'name: \'' . $defect['title'] . '\',';
                $bar .= 'y: ' . $defect['cnt'];
                if ($first == 0)
                    $bar .= ",sliced: true, selected: true" . PHP_EOL;
                $bar .= '}';
                $first++;
            }
            $bar .= "]}]";
            echo $bar;
            ?>
        });
</script>
