<?php

use common\models\City;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

$cities = City::find()->all();
$items = ArrayHelper::map($cities, 'uuid', 'title');

$city = '';
$street = '';
$house = '';

echo '<label>Адрес</label></br>';
echo '<label>Город</label></br>';
echo Select2::widget(
    ['id' => 'city',
        'name' => 'city',
        'data' => $items,
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Выберите город..'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
        'pluginEvents' => [
            "select2:select" => "function(data) { 
                $('#city2').val(data.params.data.id).trigger('change');
                        $.ajax({
                                url: '../city/streets',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    var streets = JSON.parse(data);
                                    var select = document.getElementById('streets');                                    
                                    select.options.length = 0;
                                    for(index in streets) {
                                        select.options[select.options.length] = new Option(streets[index], index);
                                    }
                                    select = document.getElementById('streets2');
                                    if (select) {                                                                        
                                        select.options.length = 0;
                                        for(index in streets) {
                                            select.options[select.options.length] = new Option(streets[index], index);
                                        }
                                    }                                    
                                }
                            });
                  }"]
    ]);
echo '<label>Улица</label></br>';
echo Select2::widget([
    'id' => 'streets',
    'name' => 'streets',
    'data' => [],
    'language' => 'ru',
    'options' => [
        'placeholder' => 'Выберите улицу..'
    ],
    'pluginOptions' => [
        'allowClear' => true
    ],
    'pluginEvents' => [
        "select2:select" => "function(data) { 
            $('#streets2').val(data.params.data.id).trigger('change');
            $.ajax({
                                url: '../city/houses',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    var houses = JSON.parse(data);
                                    var select = document.getElementById(\"houses\");
                                    select.options.length = 0;
                                    for(index in houses) {
                                        select.options[select.options.length] = new Option(houses[index], index);
                                    }               
                                    select = document.getElementById(\"houses2\");
                                    if (select) {
                                        select.options.length = 0;
                                        for(index in houses) {
                                            select.options[select.options.length] = new Option(houses[index], index);
                                        }            
                                    }   
                                }
                            });
            }"]
]);
echo '<label>Дом</label></br>';
echo Select2::widget([
    'id' => 'houses',
    'name' => 'houses',
    'data' => [],
    'language' => 'ru',
    'options' => [
        'placeholder' => 'Выберите дом..'
    ],
    'pluginOptions' => [
        'allowClear' => true
    ],
    'pluginEvents' => [
        "select2:select" => "function(data) { 
            $('#houses2').val(data.params.data.id).trigger('change');
                        $.ajax({
                                url: '../city/objects',
                                type: 'post',
                                data: {
                                    id: data.params.data.id,
                                    type: 1
                                },
                                success: function (data) {
                                    var objects = JSON.parse(data);
                                    var select = document.getElementById(\"request-objectuuid\");
                                    if (select) {
                                        select.options.length = 0;
                                        for(index in objects) {
                                            select.options[select.options.length] = new Option(objects[index], index);
                                        }
                                    }
                                    select = document.getElementById(\"equipment-objectuuid\");
                                    if (select) {
                                        select.options.length = 0;
                                        for(index in objects) {
                                            select.options[select.options.length] = new Option(objects[index], index);
                                        }
                                    }
                                    
                                    select = document.getElementById(\"objectUuid\");
                                    if (select) {
                                        select.options.length = 0;
                                        for(index in objects) {
                                            select.options[select.options.length] = new Option(objects[index], index);
                                        }
                                    }                
                                }
                            });
            }"]
]);
