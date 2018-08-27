<?php
/* @var $ordersStatusCount
 * @var $ordersStatusPercent
 * @var $sumOrderStatusCount
 * @var $sumOrderStatusCompleteCount
 * @var $sumTaskStatusCount
 * @var $sumTaskStatusCompleteCount
 * @var $sumStageStatusCount
 * @var $sumStageStatusCompleteCount
 * @var $sumOperationStatusCount
 * @var $sumOperationStatusCompleteCount
 * @var $categories
 * @var $bar
 * @var $orders
 * @var $equipments \common\models\Equipment[]
 * @var $equipmentsCount
 * @var $messagesChat
 * @var $usersCount
 * @var $currentUser
 * @var $objectsCount
 * @var $objectsTypeCount
 * @var $events
 * @var $services
 * @var $users \common\models\Users[]
 * @var $equipmentTypesCount
 * @var $modelsCount
 * @var $documentationCount
 * @var $trackCount
 * @var $objectsList
 * @var $objectsGroup
 * @var $usersList
 * @var $usersGroup
 * @var $defectsByType
 * @var $values
 */

use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Сводная');
?>

<br/>
<!-- Info boxes -->
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="/orders/table"><span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span></a>

            <div class="info-box-content">
                <span>Выполнены <?= $ordersStatusCount[2]; ?> / Не выполнены <?= $ordersStatusCount[3]; ?></span><br/>
                <span>В процессе <?= $ordersStatusCount[4]; ?> / Отменены <?= $ordersStatusCount[1]; ?></span><br/>
                <span>Новые <?= $ordersStatusCount[0]; ?></span><br/>
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
                <a href="/equipment"><span class="info-box-text">Оборудование</span></a>
                <span><a href="/equipment-type">Типов <?= $equipmentTypesCount; ?></a> /
                    <a href="/equipment-model">Моделей <?= $modelsCount; ?></a></span><br/>
                <span class="info-box-number"><?= $equipmentsCount ?></span>
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
            <a href="/objects"><span class="info-box-icon bg-green"><i class="fa fa-map-marker"></i></span></a>

            <div class="info-box-content">
                <span class="info-box-text">Объекты</span>
                <span><a href="/object-type">Типов <?= $objectsTypeCount; ?></a><br/>
                <span class="info-box-number"><?= $objectsCount ?></span>
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
                <span class="info-box-number"><?= $usersCount ?>/<?= $usersCount ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-7">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Статистика нарядов</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-wrench"></i></button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/orders">Наряды</a></li>
                            <li><a href="/orders-calendar">Календарь нарядов</a></li>
                            <li class="divider"></li>
                            <li><a href="/analytics">Анализ выполнения</a></li>
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
                        <p class="text-center">
                            <strong>Расклад нарядов по месяцам за последний год</strong>
                        </p>
                        <div class="chart">
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
                                        pointFormat: '{series.name}: {point.y}<br/>Всего: {point.stackTotal}'
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
                                            text: 'Количество нарядов по месяцам'
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
                        <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>

            <!-- ./box-body -->
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">

                        <p class="text-center">
                            <strong>Расклад по статусам</strong>
                        </p>

                        <div class="progress-group">
                            <span class="progress-text">Новых и в процессе</span>
                            <span class="progress-number"><b><?= $ordersStatusCount[0]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-aqua" style="width: <?= number_format($ordersStatusPercent[0],0); ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Не выполнено и отменено</span>
                            <span class="progress-number"><b><?= $ordersStatusCount[1]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-red" style="width: <?= number_format($ordersStatusPercent[1],0); ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Всего выполнено</span>
                            <span class="progress-number"><b><?= $ordersStatusCount[2]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-green" style="width: <?= number_format($ordersStatusPercent[2],0); ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Не закончено</span>
                            <span class="progress-number"><b><?= $ordersStatusCount[3]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-yellow" style="width: <?= number_format($ordersStatusPercent[3],0); ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> <?php if ($sumOrderStatusCount>0) echo number_format( $sumOrderStatusCompleteCount*100/$sumOrderStatusCount,2).'%' ?> </span>
                            <h5 class="description-header"><?= $sumOrderStatusCount ?> / <?= $sumOrderStatusCompleteCount ?></h5>
                            <span class="description-text">Всего нарядов / Выполнено</span>
                        </div>
                        <div class="description-block border-right">
                            <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> <?php if ($sumTaskStatusCount>0) echo number_format( $sumTaskStatusCompleteCount*100/$sumTaskStatusCount,2).'%' ?></span>
                            <h5 class="description-header"><?= $sumTaskStatusCount ?> / <?= $sumTaskStatusCompleteCount ?></h5>
                            <span class="description-text">Задач / Выполнено</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> <?php if ($sumStageStatusCount>0) echo number_format( $sumStageStatusCompleteCount*100/$sumStageStatusCount,2).'%' ?></span>
                            <h5 class="description-header"><?= $sumStageStatusCount ?> / <?= $sumStageStatusCompleteCount ?></h5>
                            <span class="description-text">Этапов / Выполнено</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block">
                            <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> <?php if ($sumOperationStatusCount>0) echo number_format( $sumOperationStatusCompleteCount*100/$sumOperationStatusCount,2) ?></span>
                            <h5 class="description-header"><?= $sumOperationStatusCount ?> / <?= $sumOperationStatusCompleteCount ?></h5>
                            <span class="description-text">Операций / Выполнено</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
    <div class="col-md-5">
        <!-- TABLE: SERVICES STATUS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Сервисы</h3>
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
                            <th>Статус</th>
                            <th>Сервис</th>
                            <th>Запуск</th>
                            <th>Сообщение</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($services as $service) {
                            print '<tr>';
                            if ($service["status"] == '0')
                                print '<td><span class="label label-warning">Не запущен</span></td>';
                            if ($service["status"] == '1')
                                print '<td><span class="label label-success">Запущен</span></td>';
                            print '<td>' . $service["service_name"] . '</td>
                                    <td>' . $service["last_start_date"] . '</td>';
                            if ($service->last_message_type == 0)
                                print  "<td><span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>&nbsp;" .
                                    $service->last_message.'</td>';
                            if ($service->last_message_type == 1)
                                print "<td><span class='badge' style='background-color:yellow; height: 12px; margin-top: -3px'> </span>&nbsp;" .
                                    $service->last_message.'</td>';
                            if ($service->last_message_type == 2)
                                return "<td><span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span>&nbsp;" .
                                    $service->last_message.'</td>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer text-center">
                    <a href="/service" class="btn btn-sm btn-info btn-flat pull-left">Управление сервисами</a>
                </div>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
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
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
        <div class="row">
            <div class="col-md-6">
                <!-- DIRECT CHAT -->
                <div class="box box-warning direct-chat direct-chat-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Вести с полей</h3>

                        <div class="box-tools pull-right">
                            <span data-toggle="tooltip" title="3 New Messages" class="badge bg-yellow">3</span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Контакты"
                                    data-widget="chat-pane-toggle">
                                <i class="fa fa-comments"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">
                            <?php
                            foreach ($messagesChat as $message) {
                                echo '<!-- Message -->';
                                if ($message['fromUser']==$currentUser['uuid'])
                                    echo '<div class="direct-chat-msg">
                                          <div class="direct-chat-info clearfix">
                                          <span class="direct-chat-name pull-right">'.$message['from'].'</span>
                                          <span class="direct-chat-timestamp pull-left">'.$message['date'].'</span>';
                                else
                                    echo '<div class="direct-chat-msg right">
                                          <div class="direct-chat-info clearfix">
                                          <span class="direct-chat-name pull-left">'.$message['from'].'</span>
                                          <span class="direct-chat-timestamp pull-right">'.$message['date'].'</span>';
                                echo '</div>
                                        <img class="direct-chat-img" src="'.$message['fromImage'].'" alt="user">
                                        <div class="direct-chat-text">'.$message['text'].'</div></div>';
                                echo '<!-- /.direct-chat-msg -->';
                                }
                            ?>
                        </div>
                        <!--/.direct-chat-messages-->

                        <!-- Contacts are loaded here -->
                        <div class="direct-chat-contacts">
                            <ul class="contacts-list">
                                <?php
                                $count=0;
                                foreach ($users as $user) {
                                    $path = $user->getImageUrl();
                                    if ($path == null) {
                                        $path='/images/unknown2.png';
                                    }
                                    print '<li>
                                            <a href="#">
                                            <img class="contacts-list-img" src="'.Html::encode($path).'" alt="User Image">
                                            <div class="contacts-list-info">
                                            <span class="contacts-list-name">'.$user['name'].'
                                            <small class="contacts-list-date pull-right">'.$user['createdAt'].'</small>
                                            </span>
                                            <span class="contacts-list-msg">'.$user['whoIs'].'</span>
                                            </div>
                                            </a>
                                            </li>';
                                    }
                                ?>
                                <!-- End Contact Item -->
                            </ul>
                            <!-- /.contatcts-list -->
                        </div>
                        <!-- /.direct-chat-pane -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <form action="#" method="post">
                            <div class="input-group">
                                <input type="text" name="message" placeholder="Сообщение ..." class="form-control">
                                <span class="input-group-btn">
                            <button type="button" class="btn btn-warning btn-flat">Отправить</button>
                          </span>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-footer-->
                </div>
                <!--/.direct-chat -->
            </div>
            <!-- /.col -->

            <div class="col-md-6">
                <!-- USERS LIST -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Операторы</h3>

                        <div class="box-tools pull-right">
                            <span class="label label-info">Операторов: <?= count($users) ?></span>
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
                            $count=0;
                            foreach ($users as $user) {
                                $path = $user->getImageUrl();
                                if (!$path) {
                                    $path='/images/unknown2.png';
                                }
                                print '<li><img src="'.Html::encode($path).'" alt="User Image">';
                                echo Html::a(Html::encode($user['name']),
                                    ['/users/view', 'id' => Html::encode($user['_id'])],['class' => 'users-list-name']);
                                echo '<span class="users-list-date">'.$user['createdAt'].'</span></li>';
                                }
                            ?>
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <?php echo Html::a('Все операторы', ['/users/dashboard'],
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
                <h3 class="box-title">Последние наряды</h3>

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
                            <th>ID наряда</th>
                            <th>Дата</th>
                            <th>Название</th>
                            <th>Статус</th>
                            <th>Исполнитель</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count=0;
                            foreach ($orders as $order) {
                                print '<tr><td><a href="/orders/view?id='.$order["_id"].'">'.$order["_id"].'</a></td>
                                        <td>'.$order["startDate"].'</td>
                                        <td>'.$order["title"].'</td>';
                                if ($order["orderStatus"]->title=='Новый')
                                    print '<td><span class="label label-info">'.$order["orderStatus"]->title.'</span></td>';
                                else if ($order["orderStatus"]->title=='Выполнен')
                                        print '<td><span class="label label-success">'.$order["orderStatus"]->title.'</span></td>';
                                else if ($order["orderStatus"]->title=='Не выполнен')
                                    print '<td><span class="label label-danger">'.$order["orderStatus"]->title.'</span></td>';
                                else if ($order["orderStatus"]->title=='Отменен')
                                    print '<td><span class="label label-warning">'.$order["orderStatus"]->title.'</span></td>';
                                else
                                    print '<td><span class="label label-info">'.$order["orderStatus"]->title.'</span></td>';

                                print '<td><div class="sparkbar" data-color="#00a65a" data-height="20">'.$order['user']->name.'</div></td></tr>';
                                $count++;
                                if ($count>7) break;
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="/orders/create" class="btn btn-sm btn-info btn-flat pull-left">Сформировать наряд</a>
                <a href="/orders/table" class="btn btn-sm btn-default btn-flat pull-right">Посмотреть все наряды</a>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </div>

    <!-- /.col -->

    <div class="col-md-4">
        <!-- Info Boxes Style 2 -->
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-plus-square"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Оборудование</span>
                <span class="info-box-number"><?= $equipmentsCount ?></span>

                <div class="progress">
                    <div class="progress-bar" style="width: 50%"></div>
                </div>
                <span class="progress-description">
                    По <?= $equipmentTypesCount ?> типам
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-map-marker"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Треки</span>
                <span class="info-box-number"><?= $trackCount ?></span>

                <div class="progress">
                    <div class="progress-bar" style="width: 20%"></div>
                </div>
                <span class="progress-description">
                    По <?= $usersCount ?> пользователям
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-book"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Документация</span>
                <span class="info-box-number"><?= $documentationCount ?></span>

                <div class="progress">
                    <div class="progress-bar" style="width: 40%"></div>
                </div>
                <span class="progress-description">
                    По <?= $modelsCount ?> моделям
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Дефекты</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="chart-responsive">
                            <div id="container2" style="min-width:400px; width:100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.box -->

        <!-- PRODUCT LIST -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Недавно добавленное оборудование</h3>

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
                        $path = $equipment->getImageUrl();
                        if ($path==null)
                            $path = '/storage/order-level/no-image-icon-4.png';
                        print '<li class="item">
                                <div class="product-img">
                                    <img src="'.Html::encode($path).'" alt="'.$equipment['title'].'">
                                </div>
                                <div class="product-info">
                                    <a href="/equipment/view?id='.$equipment["_id"].'" class="product-title">'.$equipment["inventoryNumber"].'
                                    <span class="label label-warning pull-right">'.$equipment["title"].'</span></a>
                                    <span class="product-description">'.$equipment["equipmentModel"]->title.'</span>
                                </div></li>';
                    }
                    ?>
                    <!-- /.item -->
                </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
                <?php echo Html::a('Все оборудование', ['/equipment'],
                    ['class' => 'btn btn-sm btn-info btn-flat pull-left']); ?>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.content-wrapper -->

<footer class="main-footer" style="margin-left: 0px !important;">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.1.3
    </div>
    <?php echo Html::a('<img src="images/toir-logo_4x_m.png">', 'http://toirus.ru'); ?>
    <strong>Copyright &copy; 2014-2018 <a href="http://toirus.ru">ТОиРУС</a>.</strong> Все права на
    программный продукт защищены.
</footer>

<script>
    var userIcon = L.icon({
        iconUrl: '/images/worker_male1600.png',
        iconSize: [35, 35],
        iconAnchor: [22, 94],
        popupAnchor: [-3, -76]
    });

    <?php
    echo $objectsList;
    echo $objectsGroup;
    echo $usersList;
    echo $usersGroup;
    ?>

    var overlayMapsA = {
    };
    var overlayMapsB = {
        "Пользователи": users,
        "Объекты": objects
    };
    var map = L.map('mapid',{zoomControl: false, layers: [users, objects]}).setView([55.2969, 61.5157], 13);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        id: 'mapbox.streets'
    }).addTo(map);

    L.control.layers(overlayMapsA, overlayMapsB, {
        position:'bottomleft'
    }).addTo(map);

    L.control.zoom({
        position:'bottomleft'
    }).addTo(map);

</script>
<script type="text/javascript">
    Highcharts.chart('container2', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
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
