<?php

/* @var $usersList */
/* @var $housesList */
/* @var $housesGroup */
/* @var $coordinates */
/* @var $ways */
/* @var $users */
/* @var $usersGroup */
/* @var $wayUsers */

$this->title = Yii::t('app', 'Карта');
$this->registerJs('$(window).on("resize", function () { $("#mapid").height($(window).height()-40); map.invalidateSize(); }).trigger("resize");');

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

<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
        integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
        crossorigin=""></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css"
      integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
      crossorigin=""/>
<div class="box-relative">
        <div id="mapid" style="width: 100%; height: 800px"></div>
</div>

    <script>
        var userIcon = L.icon({
            iconUrl: '/images/worker_male1600.png',
            iconSize: [35, 35],
            iconAnchor: [14, 35],
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
        var map = L.map('mapid', {zoomControl: false, layers: [users, houses, ways]}).setView(<?= $coordinates ?>, 16);
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
