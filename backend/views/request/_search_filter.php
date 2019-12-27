<?php

use common\models\City;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form2'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Адрес</h4>
</div>
<div class="modal-body">
    <table style="width: 100%">
        <tr>
            <td style="width: 48%; vertical-align: top">
                <?php
                $cities = City::find()->all();
                $items = ArrayHelper::map($cities, 'uuid', 'title');

                $city = '';
                $street = '';
                $house = '';

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
                            refreshStreets(data.params.data.id); 
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
                            refreshHouse(data.params.data.id);
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
                            refreshObjects(data.params.data.id); 
                    }"]
                ]);


                echo '<label>Квартира/помещение</label></br>';
                echo Select2::widget([
                    'id' => 'objectUuid',
                    'name' => 'objectUuid',
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите объект..'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);

                $this->registerJs('function refreshHouse(street) {
                    $.ajax({
                        url: \'../city/houses\',
                        type: \'post\',
                        data: {
                            id: street
                        },
                        success: function (data) {
                            var houses = JSON.parse(data);
                            var select = document.getElementById("houses");
                            select.options.length = 0;
                            refreshObjects(Object.keys(houses)[0]);
                            for(index in houses) {
                                select.options[select.options.length] = new Option(houses[index], index);
                            }               
                        }   
                    });
                }');

                $this->registerJs('function refreshStreets(city) {
                    $.ajax({
                        url: \'../city/streets\',
                        type: \'post\',
                        data: {
                            id: city
                        },
                        success: function (data) {
                            var streets = JSON.parse(data);
                            var select = document.getElementById(\'streets\');                                    
                            select.options.length = 0;
                            refreshHouse(Object.keys(streets)[0]);                            
                            for(index in streets) {
                                select.options[select.options.length] = new Option(streets[index], index);
                            }                                    
                        }
                    });
                }');

                $this->registerJs('function refreshObjects(house) {
                    $.ajax({
                        url: \'../city/objects\',
                        type: \'post\',
                        data: {
                            id: house,
                            type: "flats"
                        },
                        success: function (data) {
                        var objects = JSON.parse(data);
                        var select = document.getElementById("objectUuid");
                        if (select) {
                            select.options.length = 0;
                            select.options[select.options.length] = new Option("нет", 0);
                            for(index in objects) {
                                select.options[select.options.length] = new Option(objects[index], index);
                            }
                        }
                    }
                });
                }');
                ?>
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Выбрать'), ['class' => 'btn btn-success']) ?>
</div>

<script>
    $(document).on("beforeSubmit", "#form2", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            //url: "/request/index?objectUuid="+$('#objectUuid').val(),
            type: "post",
            data: {
                house: $('#houses').val(),
                object: $('#objectUuid').val()
            },
            success: function () {
                $('#modalFilter').modal('hide');
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
