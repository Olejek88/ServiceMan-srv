<?php
/* @var $form
 * @var $model
 * @var $equipmentUuid
 */

use common\models\City;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

$cities = City::find()->all();
$items = ArrayHelper::map($cities, 'uuid', 'title');

$city = '';
$street = '';
$house = '';

echo '<label>Местоположение объекта</label></br>';
echo '<label>Город</label></br>';
echo Select2::widget(
    ['id' => 'city2',
        'name' => 'city2',
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
                        $.ajax({
                                url: '../city/streets',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    var streets = JSON.parse(data);
                                    var select = document.getElementById('streets2');
                                    select.options.length = 0;
                                    for(index in streets) {
                                        select.options[select.options.length] = new Option(streets[index], index);
                                    }

                                    $('#streets2').trigger(
                                        {
                                            type: 'select2:select',
                                            params: {
                                                data: {id : select.options[0].value}
                                            }
                                        }
                                    );
                                }
                            });
                  }"]
    ]);
echo '<label>Улица</label></br>';
echo Select2::widget([
    'id' => 'streets2',
    'name' => 'streets2',
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
                        $.ajax({
                                url: '../city/houses',
                                type: 'post',
                                data: {
                                    id: data.params.data.id
                                },
                                success: function (data) {
                                    var houses = JSON.parse(data);
                                    var select = document.getElementById(\"houses2\");
                                    select.options.length = 0;
                                    for(index in houses) {
                                        select.options[select.options.length] = new Option(houses[index], index);
                                    }

                                    $('#houses2').trigger(
                                        {
                                            type: 'select2:select',
                                            params: {
                                                data: {id : select.options[0].value}
                                            }
                                        }
                                    );
                                }
                            });
            }"]
]);
echo '<label>Дом</label></br>';
echo Select2::widget([
    'id' => 'houses2',
    'name' => 'houses2',
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
             $.ajax({
                  url: '../city/objects',
                  type: 'post',
                  data: {
                       id: data.params.data.id
                  },
                  success: function (data) {
                      var objects = JSON.parse(data);
                      var select = document.getElementById('object2');
                      select.options.length = 0;
                      for(index in objects) {
                          select.options[select.options.length] = new Option(objects[index], index);
                      }

                      $('#object2').trigger(
                            {
                                type: 'select2:select',
                                params: {
                                    data: {id : select.options[0].value}
                                }
                            }
                      );
                  }
                 });
            }"]
]);
echo '<label>Объект</label></br>';
echo Select2::widget([
    'id' => 'object2',
    'name' => 'object2',
    'data' => [],
    'language' => 'ru',
    'options' => [
        'placeholder' => 'Выберите объект..'
    ],
    'pluginOptions' => [
        'allowClear' => true
    ],
    'pluginEvents' => [
        "select2:select" => "function(data) { 
              $.ajax({
                   url: '../city/equipments',
                   type: 'post',
                   data: {
                        id: data.params.data.id
                   },
                   success: function (data) {
                        var objects = JSON.parse(data);
                        var select = document.getElementById('request-equipmentuuid');
                        if (select) {
                             select.options.length = 0;
                             for(index in objects) {
                                  select.options[select.options.length] = new Option(objects[index], index);
                             }
                             // это нужно для того чтобы сразу после загрузки данных ни чего не было выбрано
                             $(select).select2('val', '-')
                        }
                   }
        });
    }"]
]);
