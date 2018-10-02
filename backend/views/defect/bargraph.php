<?php

use backend\assets\AdminLteAsset;

AdminLteAsset::register($this);
/* @var $defects common\models\Defect */
$this->title = Yii::t('app', 'Распределение дефектов');

?>

<div class="measured-value-index" style="height: 700px">
<div id="container" style="min-width:1200px; width:100%; height:90%">
</div>
</div>

<script src="/js/vendor/lib/HighCharts/highcharts.js"></script>
<script src="/js/vendor/lib/HighCharts/modules/exporting.js"></script>

<script type="text/javascript">
Highcharts.chart('container', {
    chart: {
        type: 'bar',
        height: 700
    },
    title: {
        text: 'Дефекты'
    },
    subtitle: {
        text: 'Распределение дефектов по типам и годам'
    },
    xAxis: {
        categories: [
            <?php
        	//var_dump ($defects);
		    $first=0;
		    foreach ($defects as $defect) {
		        if ($first>0) echo ',';
		        echo "'".$defect['title']."'";
		        $first++;
		    }
	        ?>
        ],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Количество дефектов',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' '
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: [{
	<?php
	    $first=0;
	    $bar="name: 'Дефекты',";
	    $bar.="data: [";
    	    foreach ($defects as $defect) {
		if ($first>0) $bar.=",".PHP_EOL;
//		$bar.="{".PHP_EOL;
//		$bar.="name: '".$defect['title']."',";
//		$bar.="data: '".$defect['cnt']."'";
		$bar.=$defect['cnt'];
//		$bar.="}";
		$first++;
		}
	    $bar.="]}";
	    echo $bar;
	?>
	]
});

</script>
