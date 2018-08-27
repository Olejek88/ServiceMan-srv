<?php
use backend\assets\AdminLteAsset;
AdminLteAsset::register($this);
?>

<div class="measured-value-index">
    <div id="container" style="min-width:1200px; width:100%; height:90%">
    </div>
</div>

<script src="js/vendor/lib/HighCharts/highcharts-gantt.src.js"></script>

<script type="text/javascript">
    var today = new Date(),
        day = 1000 * 60 * 60 * 24;

    // Set to 00:00:00:000 today
    today.setUTCHours(0);
    today.setUTCMinutes(0);
    today.setUTCSeconds(0);
    today.setUTCMilliseconds(0);
    today = today.getTime();

    //document.write (today);

    var series = [{
        name: 'Наряды ремонтных групп',
        data: [<?php echo $chart; ?>]
    }];
    var title = 'Задачи сотрудников';
    var xAxis = {
        currentDateIndicator: true,
        scrollbar: {
            enabled: true
        },
        min: today - 30 * day,
        max: today + 10 * day
    };
    // THE CHART
    var createChart = function (title, xAxis, series) {
        Highcharts.setOptions({
            lang: {
                months: [
                    'Январь', 'Февраль', 'Март', 'Апрель',
                    'Май', 'Июнь', 'Июль', 'Август',
                    'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                ],
                weekdays: [
                    'Воскресение', 'Понедельник', 'Вторник', 'Среда',
                    'Четверг', 'Пятница', 'Суббота'
                ]
            }
        });
        return Highcharts.ganttChart('container', {
            title: {
                text: title
            },
            xAxis: xAxis,
            series: series
        });
    };

    var chart = createChart(title, xAxis, series);
</script>

<div class="measured-value-index">
</div>

<script type="text/javascript">
    var today = new Date(),
        day = 1000 * 60 * 60 * 24;

    // Set to 00:00:00:000 today
    today.setUTCHours(0);
    today.setUTCMinutes(0);
    today.setUTCSeconds(0);
    today.setUTCMilliseconds(0);
    today = today.getTime();

    //document.write (today);

    var series = [{
        name: 'Наряды ремонтных групп',
        data: [
            <?php
            echo $chart;
            ?>
        ]
    }];
    var title = 'Задачи сотрудников';
    var xAxis = {
        currentDateIndicator: true,
        scrollbar: {
            enabled: true
        },
        min: today - 30 * day,
        max: today + 10 * day
    };
    // THE CHART
    var createChart = function (title, xAxis, series) {
        Highcharts.setOptions({
            lang: {
                months: [
                    'Январь', 'Февраль', 'Март', 'Апрель',
                    'Май', 'Июнь', 'Июль', 'Август',
                    'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                ],
                weekdays: [
                    'Воскресение', 'Понедельник', 'Вторник', 'Среда',
                    'Четверг', 'Пятница', 'Суббота'
                ]
            }
        });
        return Highcharts.ganttChart('container', {
            title: {
                text: title
            },
            xAxis: xAxis,
            series: series
        });
    };

    createChart(title, xAxis, series);
</script>
