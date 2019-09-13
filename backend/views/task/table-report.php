<?php
/* @var $bar
 * @var $categories
 * @var $users
 * @var $usersAll
 * @var $systemAll
 */

use kartik\select2\Select2;
use kartik\widgets\DatePicker;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Отчет по исполнителям');

$start_date = '2018-12-31';
$end_date = '2021-12-31';
$user = '';
$system = '';
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];
if (isset($_GET['user_select']))
    $user = $_GET['user_select'];
if (isset($_GET['system_select']))
    $system = $_GET['system_select'];

?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <form action="">
        <table style="width: 1200px; padding: 3px">
            <tr>
                <td style="width: 300px">
                    <?php
                    echo DatePicker::widget([
                            'name' => 'start_time',
                            'value' => $start_date,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]) . '</td><td style="width: 300px">' .
                        DatePicker::widget([
                            'name' => 'end_time',
                            'value' => $end_date,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]) . '<td style="width: 300px">' .
                        Select2::widget([
                        'id' => 'user_select',
                        'name' => 'user_select',
                        'value' => $user,
                        'language' => 'ru',
                        'data' => $usersAll,
                        'options' => ['placeholder' => 'Выберите пользователя...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) .'</td><td>&nbsp;</td>
                    <td style="width: 300px">'.
                        Select2::widget([
                        'id' => 'system_select',
                        'name' => 'system_select',
                        'value' => $system,
                        'language' => 'ru',
                        'data' => $systemAll,
                        'options' => ['placeholder' => 'Выберите систему...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        ]) .'</td><td>&nbsp;</td><td style="width: 100px">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
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
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="3"><div class="progress"><div class="critical6">' . $user['total'] . '</div></div></td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="4"><div class="progress"><div class="critical4">' . $user['complete'] . '</div></div></td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="5"><div class="progress"><div class="critical3">' . $user['complete_good'] . '</div></div></td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="6"><div class="progress"><div class="critical1">' . $user['bad'] . '</div></div></td>';
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
