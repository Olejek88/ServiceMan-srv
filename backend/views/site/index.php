<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $accountUser */
/* @var $orders */
/* @var $equipment */
/* @var $js */
/* @var $js2 */
/* @var $gps */
/* @var $journal */
/* @var $activeUserLog */
/* @var $objectsList */
/* @var $objectsGroup */
/* @var $usersList */
/* @var $photoHouses */
/* @var $photosGroup */
/* @var $photosList */
/* @var $equipmentsList */
/* @var $equipmentsGroup */
/* @var $ways */
/* @var $users */
/* @var $usersGroup */
/* @var $wayUsers */

$this->title = Yii::t('app', 'Карта');

?>

<div id="page-preloader">
    <div class="cssload-preloader cssload-loading">
        <span class="cssload-slice"></span>
        <span class="cssload-slice"></span>
        <span class="cssload-slice"></span>
        <span class="cssload-slice"></span>
        <span class="cssload-slice"></span>
        <span class="cssload-slice"></span>
    </div>
</div>

<div class="box-relative">

    <div class="control-panel-user">
        <nav class="navbar navbar-default" style="margin: 0 auto; width: 920px;">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">СЕРВИС</a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">Управление <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li role="presentation" class="dropdown-header">Основные разделы</li>
                                <li><a href="/alarm">Аварии</a></li>
                                <li><a href="/street">Улицы</a></li>
                                <li><a href="/house">Дома</a></li>
                                <li><a href="/flat">Квартиры</a></li>
                                <li class="divider"></li>
                                <li role="presentation" class="dropdown-header">Единицы</li>
                                <li><a href="/users">Пользователи</a></li>
                                <li><a href="/equipment">Оборудование</a></li>
                                <li><a href="/residents">Абоненты</a></li>
                                <li><a href="/measure">Измерения</a></li>
                                <li class="divider"></li>
                                <li role="presentation" class="dropdown-header">Справочники</li>
                            </ul>
                        </li>
                    </ul>
                    <form class="navbar-form navbar-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control search-model" id="autofocus-none" placeholder="Поиск"
                                   style="width: 280px;" disabled="disabled">
                        </div>
                        <!-- <button type="submit" class="btn btn-default">Найти</button> -->
                    </form>
                    <ul class="nav navbar-nav navbar-right">

                        <?= $menuItems[] = '<li>'
                            . Html::beginForm(['/logout'], 'post')
                            . Html::submitButton(
                                'Выход',
                                [
                                    'class' => 'btn btn-link logout',
                                    'style' => 'padding: 25px 10px 20px 10px;'
                                ]
                            )
                            . Html::endForm()
                            . '</li>';
                        ?>

                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <div class="control-panel-user-tool">
        <div class="btn-group btn-group-justified">
            <a href="#" class="btn btn-default dropdown-toggle sync" data-toggle="tooltip" data-placement="bottom"
               title="" data-original-title="Журнал событий">Журнал</a>
            <!-- <a href="#" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Цели текущей смены">Цели <span class="badge">3</span></a>
            <a href="#" class="btn btn-default">Геоцентр</a> -->
        </div>
    </div>

    <div class="modal model-search" id="model-search" data-backdrop="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center">Расширенный поиск</h4>
                    <input type="text" class="form-control" id="order-filtr-input" v-model="input"
                           placeholder="Введите ваш запрос" autofocus>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs header-result-panel" style="width: 148px;">
                        <li class="active"><a href="#punkt-1" data-toggle="tab">Пункт 1</a></li>
                        <li><a href="#punkt-2" data-toggle="tab">Пункт 2</a></li>
                    </ul>

                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="punkt-1">
                            <h6 class="text-center">Данный раздел находится в разработке..</h6>
                        </div>
                        <div class="tab-pane fade" id="punkt-2">
                            <h6 class="text-center">Данный раздел находится в разработке..</h6>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal model-settings" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center">Пользовательские данные</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs header-result-panel" style="width: 350px;">
                        <li class="active"><a href="#common" data-toggle="tab">Основное</a></li>
                        <!-- <li><a href="#secuire" data-toggle="tab">Безопасность</a></li> -->
                        <li><a href="#history_active" data-toggle="tab">История активности</a></li>
                    </ul>

                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="common">
                            <form class="form-horizontal">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-lg-2 control-label">Почта</label>
                                        <div class="col-lg-10">
                                            <?php if (isset($accountUser->email)): ?>
                                                <input type="text" class="form-control" id="inputEmail"
                                                       value="<?= Html::encode($accountUser->email) ?>">
                                            <?php else: ?>
                                                <input type="text" class="form-control" id="inputEmail">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-lg-2 control-label">Логин</label>
                                        <div class="col-lg-10">
                                            <?php if (isset($accountUser->username)): ?>
                                                <input type="text" class="form-control" id="inputEmail"
                                                       value="<?= Html::encode($accountUser->username) ?>">
                                            <?php else: ?>
                                                <input type="text" class="form-control" id="inputEmail">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-lg-2 control-label">Регистрации</label>
                                        <div class="col-lg-10">
                                            <?php if (isset($accountUser->created_at)): ?>
                                                <input type="text" class="form-control" id="inputEmail"
                                                       value="<?= Html::encode(date("F j, Y, g:i a", $accountUser->created_at)) ?>">
                                            <?php else: ?>
                                                <input type="text" class="form-control" id="inputEmail">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <div class="col-lg-12">
                                            <?php if (isset($accountUser->created_at)): ?>
                                                <a href="/user/update?id=<?= Html::encode(Yii::$app->user->identity->attributes['id']) ?>"
                                                   class="btn btn-primary">
                                                    Изменить
                                                </a>
                                            <?php else: ?>
                                                <a href="#">
                                                    Изменить
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <!-- <div class="tab-pane fade" id="secuire">
                            <h6 class="text-center">Данный раздел находится в разработке..</h6>
                        </div> -->
                        <div class="tab-pane fade" id="history_active">
                            <h6 class="text-center">
                                <?php if (!empty($activeUserLog)): ?>
                                    <?php
                                    $countLog = count($activeUserLog);
                                    ?>
                                    <table class="table table-striped table-hover text-left">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Email</th>
                                            <th>Адрес</th>
                                            <th>Время</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($activeUserLog as $key => $value): ?>
                                            <tr>
                                                <td><?= Html::encode($countLog - $key) ?></td>
                                                <td><?= Html::encode($activeUserLog[$key]['userId']) ?></td>
                                                <td><?= Html::encode($activeUserLog[$key]['address']) ?></td>
                                                <td><?= Html::encode($activeUserLog[$key]['date']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <h6 class="text-center">
                                        История активности отсутствует
                                    </h6>
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="filtr-list">
        <div class="modal model-orders" data-backdrop="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title text-center">Активные наряды</h4>
                        <input type="text" class="form-control" id="order-filtr-input" v-model="input"
                               placeholder="Введите ключые слова для фильтрации" autofocus>
                    </div>
                    <div class="modal-body orders-block" id="orders">
                        <ul class="list-group" v-for="item in orders | orderBy 'changedAt' -1 | filterBy input">
                            <a href="/orders/{{ item._id }}">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon" style="background: #ccc; height: 106px;"><i
                                                class="fa fa-cogs"></i></span>
                                    <div class="info-box-content" style="background: #778899;">
                                        <span class="info-box-number">{{ item.title }}</span>
                                        <span class="info-box-text">{{ item.orderStatusUuid }} | {{ item.orderVerdictUuid }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 60%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ item.createdAt }} | {{ item.changedAt }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal model-objects" data-backdrop="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title text-center">Активные объекты</h4>
                        <input type="text" class="form-control" id="order-filtr-input" v-model="input"
                               placeholder="Введите ключые слова для фильтрации" autofocus>
                    </div>
                    <div class="modal-body orders-block" id="orders">
                        <ul class="list-group" v-for="item in equipment | orderBy 'changedAt' -1 | filterBy input">
                            <a href="/equipment/view?id={{ item._id }}">
                                <div class="info-box bg-green" style="background: #708090;">
                                    <span class="info-box-icon"
                                          style="height: 105px; width: 105px; background-image: url(<?= "{{ item.image }}" ?>); display: inline-block; background-size: 100px; background-repeat: no-repeat;">
<!--                                        <img src="{{ item.image }}" alt="Images" style="height: 105px; width: 100px;">-->
                                    </span>
                                    <div class="info-box-content" style="background: #708090;">
                                        <span class="info-box-number">{{ item.title }}</span>
                                        <span class="info-box-text">{{ item.equipmentModelUuid }} | {{ item.equipmentStatusUuid }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 60%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ item.createdAt }} | {{ item.changedAt }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="mapid" style="width: 100%; height: 800px"></div>

        <div class="close-panel-list"
             style="position: absolute; z-index: 2100; bottom: 50px; width: 900px; left: 50%; margin-left: -450px;">

            <div class="modal-content">
                <div class="modal-header" style="padding: 18px 20px 0 20px">
                    <button type="button" class="close btn btn-box-tool close-panel-j">
                        <i class="fa fa-times"></i>
                    </button>
                    <h6>События журнала за последние 24 часа</h6>
                </div>
                <div class="modal-body">
                    <?php Pjax::begin(['enablePushState' => false]); ?>
                    <?= Html::a("Обновить", ['site/index'], ['id' => 'refresh', 'class' => 'btn btn-lg btn-primary hidden']) ?>
                    <?php if (!empty($journal)): ?>
                        <footer class="panel-last-event" style="margin: 0;">
                            <div class="block-table-journal table-responsive">
                                <div class="DivFixTable">
                                    <div class="Tdata">
                                        <table id="MyTable"
                                               class="table table-bordered table-hover table-condensed all-tables">
                                            <col width=10>
                                            <col width=60>
                                            <col width=80>
                                            <col width=200>
                                            <?php foreach ($journal as $j => $value): ?>
                                                <tr class="success">
                                                    <td><?= Html::encode(count($journal) - $j) ?></td>
                                                    <td><?= Html::encode($journal[$j]['date']) ?></td>
                                                    <td><?= Html::encode($journal[$j]['userUuid']) ?></td>
                                                    <td><?= Html::encode($journal[$j]['description']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </footer>
                    <?php else: ?>
                        <footer class="panel-last-event" style="margin: 0;">
                            <div class="block-table-journal table-responsive">
                                <h5 class="text-center">Журнал событий пуст</h5>
                            </div>
                        </footer>
                    <?php endif; ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
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
            iconAnchor: [22, 94],
            popupAnchor: [-3, -76]
        });

        <?php
        echo $usersList;
        echo $usersGroup;
        echo $photosList;
        echo $photosGroup;
        echo $ways;
        $cnt = 0;
        foreach ($users as $user) {
            echo $wayUsers[$cnt];
            $cnt++;
        }

        ?>

        var overlayMapsA = {};
        var overlayMapsB = {
            "Дома": photos,
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
        var map = L.map('mapid', {zoomControl: false, layers: [users, photos]}).setView([56.0366, 59.5536], 13);
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
