<?php
/* @var $bar
 * @var $categories
 * @var $users
 */

use kartik\widgets\DateTimePicker;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Отчет по исполнителям');

$start_date = '2018-12-31';
$end_date = '2021-12-31';
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];

?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <form action="">
        <table style="width: 800px; padding: 3px">
            <tr>
                <td style="width: 300px">
                    <?php
                    echo DateTimePicker::widget([
                            'name' => 'start_time',
                            'value' => $start_date,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]) . '</td><td style="width: 300px">' .
                        DateTimePicker::widget([
                            'name' => 'end_time',
                            'value' => $end_date,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]) . '</td><td style="width: 100px">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
                            'class' => 'btn btn-success']) . '';
                    ?>
                </td>
            </tr>
        </table>
    </form>
    <br/>
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="text-center kv-align-middle" data-col-seq="0" style="width: 3%;"></th>
            <th class="text-center kv-align-middle" data-col-seq="1" style="width: 20%;">Исполнитель</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="2" style="width: 25%;">Инженерная
                система
            </th>
            <th class="text-center kv-align-middle" data-col-seq="3">Всего задач</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="4">Выполнено</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="5">Выполнено в срок</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="6">Просрочено</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($users as $user) {
            if (isset($user['count'])) {
                echo '<tr data-key="1">';
                echo '<td class="table_class kv-align-middle" style="width: 50px; text-align: center; padding: 5px 10px 5px 10px;" data-col-seq="0">'
                    . $user['count'] . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="1">' . $user['name'] . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $user['system'] . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="3"><div class="progress"><div class="critical5">' . $user['total'] . '</div></div></td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="4"><div class="progress"><div class="critical5">' . $user['complete'] . '</div></div></td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="5"><div class="progress"><div class="critical5">' . $user['complete_good'] . '</div></div></td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="6"><div class="progress"><div class="critical5">' . $user['bad'] . '</div></div></td>';
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>
<div id="container" style="height: 300px;"></div>
<script src="/js/vendor/lib/HighCharts/highcharts.js"></script>
<script src="/js/vendor/lib/HighCharts/modules/exporting.js"></script>
<script type="text/javascript">
    Highcharts.chart('container', {
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
        legend: {
            align: 'right',
            x: -300,
            verticalAlign: 'top',
            y: 0,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}'
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Количество задач по пользователям'
            }
        },
        series: [
            <?php
            echo $bar;
            ?>
        ]
    });
</script>
