<?php

/* @var $house common\models\House
 * @var $source
 * @var $streetUuid
 */

use common\components\MainFunctions;
use common\models\HouseStatus;
use common\models\HouseType;
use common\models\Users;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\plugins\geocoder\GeoCoder;
use dosamigos\leaflet\plugins\geocoder\ServiceNominatim;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\widgets\Map;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '/object/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Дом</h4>
</div>
<div class="modal-body">
    <?php
    $latDefault = 55.160374;
    $lngDefault = 61.402738;

    if ($house['uuid']) {
        echo Html::hiddenInput("houseUuid", $house['uuid']);
        echo $form->field($house, 'uuid')
            ->hiddenInput(['value' => $house['uuid']])
            ->label(false);
        $latDefault = $house['latitude'];
        $lngDefault = $house['longitude'];

    } else {
        echo $form->field($house, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
        echo $form->field($house, 'streetUuid')->hiddenInput(['value' => $streetUuid])->label(false);
        echo $form->field($house, 'houseStatusUuid')->hiddenInput(['value' => HouseStatus::HOUSE_STATUS_OK])->label(false);
    }
    echo $form->field($house, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);
    echo $form->field($house, 'number')->textInput(['maxlength' => true]);

    echo Html::hiddenInput("type", "house");
    echo Html::hiddenInput("source", $source);

    if ($house['uuid']) {
        echo $form->field($house, 'houseTypeUuid')->hiddenInput(['value' => $house['houseTypeUuid']])->label(false);
    } else {
        $types = HouseType::find()->all();
        $items = ArrayHelper::map($types, 'uuid', 'title');
        echo $form->field($house, 'houseTypeUuid')->widget(\kartik\widgets\Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите тип..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    echo $form->field($house, 'latitude')->hiddenInput(['maxlength' => true, 'value' => $latDefault])->label(false);
    echo $form->field($house, 'longitude')->hiddenInput(['maxlength' => true, 'value' => $lngDefault])->label(false);
    //echo $form->field($house, 'latitude')->textInput(['maxlength' => true, 'value' => $latDefault]);
    //echo $form->field($house, 'longitude')->textInput(['maxlength' => true, 'value' => $lngDefault]);

    // lets use nominating service
    $nominatim = new ServiceNominatim();

    // create geocoder plugin and attach the service
    $geoCoderPlugin = new GeoCoder([
        'service' => $nominatim,
        'clientOptions' => [
            // we could leave it to allocate a marker automatically
            // but I want to have some fun
            'showMarker' => false,
        ]
    ]);

    // first lets setup the center of our map
    $center = new LatLng(['lat' => $latDefault, 'lng' => $lngDefault]);

    // now lets create a marker that we are going to place on our map
    $marker = new Marker([
        'latLng' => $center,
//        'popupContent' => 'Hi!',
        'name' => 'geoMarker',
        'clientOptions' => ['draggable' => true],
        'clientEvents' => [
            'dragend' => 'function(e){
//                console.log(e.target._latlng.lat, e.target._latlng.lng);
                $("#house-latitude").val(e.target._latlng.lat);
                $("#house-longitude").val(e.target._latlng.lng);
            }'
        ],
    ]);
    // The Tile Layer (very important)
    $tileLayer = new TileLayer([
//        'urlTemplate' => 'http://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'urlTemplate' => 'http://{s}.tiles.mapbox.com/v4/mapquest.streets-mb/{z}/{x}/{y}.{ext}?access_token=pk.eyJ1IjoibWFwcXVlc3QiLCJhIjoiY2Q2N2RlMmNhY2NiZTRkMzlmZjJmZDk0NWU0ZGJlNTMifQ.mPRiEubbajc6a5y9ISgydg',
        'clientOptions' => [
            'attribution' => 'Tiles &copy; <a href="http://www.osm.org/copyright" target="_blank">OpenStreetMap contributors</a> />',
            'subdomains' => '1234',
//            'id' => 'mapbox.streets',
            'type' => 'osm',
            's' => 'a',
            'ext' => 'png',

        ]
    ]);

    // now our component and we are going to configure it
    $leafLet = new LeafLet([
        'name' => 'geoMap',
        'center' => $center,
        'tileLayer' => $tileLayer,
        'clientEvents' => [
            'geocoder_showresult' => 'function(e){
                // set markers position
                geoMarker.setLatLng(e.Result.center);
                $("#house-latitude").val(e.Result.center.lat);
                $("#house-longitude").val(e.Result.center.lng);
            }'
        ],
    ]);
    // Different layers can be added to our map using the `addLayer` function.
    $leafLet->addLayer($marker);      // add the marker
    //    $leafLet->addLayer($tileLayer);  // add the tile layer

    // install the plugin
    $leafLet->installPlugin($geoCoderPlugin);

    // finally render the widget
    try {
        echo Map::widget(['leafLet' => $leafLet]);
    } catch (Exception $exception) {
        echo '<div id="map"/>';
    }

    echo '<label class="control-label" style="font-weight: bold">Для МКД</label></br>';
    echo '<label class="control-label">Квартир</label>&nbsp;&nbsp;';
    echo Html::textInput("flats");
    echo '</br>';
    echo '<label class="control-label">Этажей</label>&nbsp;&nbsp;';
    echo Html::textInput("stages");
    echo '</br>';
    echo '<label class="control-label">Подъездов</label>&nbsp;&nbsp;';
    echo Html::textInput("entrances");
    echo '</br>';
    echo '<label class="control-label">Тип плит / наличие газа</label>';
    $types = [
        '0' => 'Электричество',
        '1' => 'Газ'
    ];
    echo Select2::widget(
        [
                'name' => 'energy',
            'data' => $types,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo '</br>';
    echo Html::checkbox('lift',true,['label' => 'Лифт']);
    echo '</br>';
    //echo Html::checkbox('water_counter',true,['label' => 'Квартирные счетчики воды']);
    //echo '</br>';
    echo Html::checkbox('balcony', true, ['label' => 'Инженерные системы квартиры']);
    echo '</br>';
    echo Html::checkbox('trash_pipe', true, ['label' => 'Мусоропровод']);
    echo '</br>';
    echo Html::checkbox('yard',true,['label' => 'Придомовая территория']);
    echo '</br>';
    echo Html::checkbox('internet',true,['label' => 'Интернет']);
    echo '</br>';
    echo Html::checkbox('tv',true,['label' => 'ТВ']);
    echo '</br>';
    echo Html::checkbox('domophones',true,['label' => 'Домофоны']);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: new FormData(this),
            processData: false,
            contentType: false
            url: "../object/save",
            success: function () {
                $('#modalAdd').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
