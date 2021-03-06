<?php
/* @var $cityCount
 * @var $streetCount
 * @var $equipmentCount
 * @var $houseCount
 * @var $equipmentTypeCount
 * @var $contragentCount
 * @var $workersCount
 * @var $measures
 * @var $equipments
 * @var $contragents
 * @var $sumStageStatusCompleteCount
 * @var $sumOperationStatusCount
 * @var $sumOperationStatusCompleteCount
 * @var $categories
 * @var $bar
 * @var $orders
 * @var $equipments Equipment[]
 * @var $messagesChat
 * @var $usersCount
 * @var $activeUsersCount
 * @var $currentUser
 * @var $objectsCount
 * @var $flatCount
 * @var $objectsTypeCount
 * @var $events
 * @var $services
 * @var $users Users[]
 * @var $equipmentTypesCount
 * @var $modelsCount
 * @var $documentationCount
 * @var $trackCount
 * @var $objectsList
 * @var $objectsGroup
 * @var $usersList
 * @var $usersGroup
 * @var $defectsByType
 * @var $usersList
 * @var $housesList
 * @var $housesGroup
 * @var $coordinates
 * @var $ways
 * @var $users
 * @var $usersGroup
 * @var $wayUsers
*/

use common\models\Equipment;
use common\models\User;
use common\models\UserContragent;
use common\models\Users;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Сводная');
?>

<br/>
<!-- Info boxes -->
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="/city"><span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span></a>

            <div class="info-box-content">
                <span>Городов <?= $cityCount; ?></span><br/>
                <span>Улиц <?= $streetCount; ?></span> / <span>Домов <?= $houseCount; ?></span><br/>
                <span>Квартир <?= $flatCount; ?></span><br/>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="/equipment"><span class="info-box-icon bg-red"><i class="fa fa-plug"></i></span></a>

            <div class="info-box-content">
                <a href="/equipment"><span class="info-box-text">Элементов</span></a>
                <span><a href="/equipment-type">Типов <?= $equipmentTypeCount; ?></a></span><br/>
                <span class="info-box-number"><?= $equipmentCount ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="/object/tree"><span class="info-box-icon bg-green"><i class="fa fa-map-marker"></i></span></a>

            <div class="info-box-content">
                <span class="info-box-text">Объекты</span>
                <span>Объектов системы <?= $objectsCount; ?></span><br/>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="/users/dashboard"><span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span></a>
            <div class="info-box-content">
                <span class="info-box-text">Пользователи</span>
                <span>Всего / Активных</span>
                <span class="info-box-number"><?= $usersCount ?>/<?= $activeUsersCount ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Статистика задач</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-wrench"></i></button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/task/report">Отчет по задачам</a></li>
                            <li class="divider"></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart">
                            <div id="container" style="height: 250px;"></div>
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
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </div>


    <div class="col-md-8">
        <!-- MAP & BOX PANE -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Карта объектов и пользователей</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-md-9 col-sm-8" style="width: 100%">
                        <div class="pad" style="padding: 1px">
                            <div id="mapid" style="width: 100%; height: 360px"></div>
                        </div>

                        <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
                                integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
                                crossorigin=""></script>
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css"
                              integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
                              crossorigin=""/>

                        <script>
                            var userIcon = L.icon({
                                iconUrl: '/images/worker_male1600.png',
                                iconSize: [35, 35],
                                iconAnchor: [22, 94],
                                popupAnchor: [-3, -76]
                            });
                            var houseIcon = L.icon({
                                iconUrl: '/images/marker_house.png',
                                iconSize: [32, 51],
                                iconAnchor: [14, 51],
                                popupAnchor: [-3, -76]
                            });

                            <?php
                            echo $usersList;
                            echo $usersGroup;
                            echo $housesList;
                            echo $housesGroup;
                            echo $ways;
                            $cnt = 0;
                            foreach ($users as $user) {
                                echo $wayUsers[$cnt];
                                $cnt++;
                            }

                            ?>

                            var overlayMapsA = {};
                            var overlayMapsB = {
                                "Дома": houses,
                                "Пользователи": users,
                                "Маршруты:": ways
                                <?php
                                $cnt = 0;
                                foreach ($users as $user) {
                                    echo ',' . PHP_EOL . '"' . $user['name'] . '": wayUser' . $user["_id"];
                                    $cnt++;
                                }
                                ?>
                            };
                            var map = L.map('mapid', {zoomControl: false, layers: [users, houses, ways]}).setView(<?= $coordinates ?>, 13);
                            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                                maxZoom: 18,
                                id: 'mapbox.streets'
                            }).addTo(map);

                            L.control.layers(overlayMapsA, overlayMapsB, {
                                position: 'bottomleft'
                            }).addTo(map);

                            L.control.zoom({
                                position: 'bottomleft'
                            }).addTo(map);

                        </script>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
        <div class="row">
            <div class="col-md-12">
                <!-- USERS LIST -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Исполнители</h3>

                        <div class="box-tools pull-right">
                            <span class="label label-info">Исполнителей: <?= $workersCount ?></span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <ul class="users-list clearfix">
                            <?php
                            $count = 0;
                            foreach ($contragents as $contragent) {
                                $userContragent = UserContragent::find()->where(['contragentUuid' => $contragent['uuid']])->one();
                                if ($userContragent && $userContragent->user->user->status == User::STATUS_ACTIVE) {
                                    if ($userContragent['user']['type'] == Users::USERS_WORKER) {
                                        $path = $userContragent['user']->getPhotoUrl();
                                        if (!$path || !$userContragent['user']['image']) {
                                            $path = '/images/unknown.png';
                                        }
                                        print '<li style="width:23%"><img src="' . Html::encode($path) . '" alt="User Image" width="145px">';
                                        echo Html::a(Html::encode($userContragent['user']['name']),
                                            ['/users/view', 'id' => Html::encode($userContragent['user']['_id'])], ['class' => 'users-list-name']);
                                        echo '<span class="users-list-date">' . $userContragent['user']['createdAt'] . '</span></li>';
                                    }
                                }
                                /*                                $path = $user->getPhotoUrl();
                            if (!$path || !$user['image']) {
                                $path = '/images/unknown.png';
                            }
                            print '<li style="width:23%"><img src="' . Html::encode($path) . '" alt="User Image" width="145px">';
                            echo Html::a(Html::encode($user['name']),
                                ['/users/view', '_id' => Html::encode($user['_id'])], ['class' => 'users-list-name']);
                            echo '<span class="users-list-date">' . $user['createdAt'] . '</span></li>';*/
                            }
                            ?>
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <?php echo Html::a('Все пользователи', ['/users/dashboard'],
                            ['class' => 'btn btn-sm btn-info btn-flat pull-left']); ?>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <!--/.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- TABLE: LATEST ORDERS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Последние измерения</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Адрес</th>
                            <th>Элементы</th>
                            <th>Данные</th>
                            <th>Исполнитель</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 0;
                        foreach ($measures as $measure) {
                            print '<tr><td><a href="/measure/view?id=' . $measure["_id"] . '">' . $measure["_id"] . '</a></td>
                                        <td>' . $measure["date"] . '</td>
                                        <td>' . $measure["equipment"]["object"]["house"]["street"]->title . ',' . $measure["equipment"]["object"]["house"]->number . ', ' . $measure["equipment"]["object"]->title . '</td>
                                        <td>' . $measure["equipment"]["equipmentType"]->title . '</td>
                                        <td>' . $measure["value"] . '</td>';
                            print '<td><div class="sparkbar" data-color="#00a65a" data-height="20">' . $measure['user']->name . '</div></td></tr>';
                            $count++;
                            if ($count > 7) break;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="/measure" class="btn btn-sm btn-default btn-flat pull-right">Отчет о показаниях</a>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </div>

    <!-- /.col -->

    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Недавно добавленные элементы</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <ul class="products-list product-list-in-box">
                    <?php
                    foreach ($equipments as $equipment) {
                        $path = '/images/no-image-icon-4.png';
                        print '<li class="item">
                                <div class="product-img">
                                    <img src="' . Html::encode($path) . '" alt="' . $equipment['equipmentType']->title . '">
                                </div>
                                <div class="product-info">
                                    <a href="/equipment/view?id=' . $equipment["_id"] . '" class="product-title">' . $equipment["serial"] . '
                                    <span class="label label-warning pull-right">' . $equipment['equipmentType']->title . '</span></a>
                                    <span class="product-description">' . $equipment["equipmentType"]->title . '</span>
                                </div></li>';
                    }
                    ?>
                    <!-- /.item -->
                </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
                <?php echo Html::a('Все элементы', ['/equipment'],
                    ['class' => 'btn btn-sm btn-info btn-flat pull-left']); ?>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.content-wrapper -->

<footer class="main-footer" style="margin-left: 0 !important;">
    <div class="pull-right hidden-xs">
        <b>Version</b> 0.0.2
    </div>
    <?php echo Html::a('<img src="/images/toir-logo_4x_m.png">', 'http://toirus.ru'); ?>
    <strong>Copyright &copy; 2014-2019 <a href="http://toirus.ru">ТОиРУС-ЖКХ</a>.</strong> Все права на
    программный продукт защищены.
</footer>