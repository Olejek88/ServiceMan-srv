<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\view
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

/* @var $this yii\web\View */
/* @var $model common\models\Orders */
/* @var $type common\models\ObjectType */

// $this->title = $model->title;

// return var_dump($model);

?>

<div class="orders-view box-padding">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0 5px;">Объекты</h3>

        <div class="panel-body">
            <header class="header-result" id="list-objects">

                <ul class="nav nav-tabs" style="width: 132px; margin: 0 auto;">
                    <li class="active">
                        <a href="#list" data-toggle="tab">Список</a>
                    </li>
                    <li class=""><a href="#type" data-toggle="tab">Типы</a></li>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="list">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Тип</th>
                                <th style="width: 350px">Описание</th>
                                <th style="width: 130px">Дата изменения</th>
                            </tr>
                            </thead>
                            <tbody v-for="model in models" style="border: 1px solid #eee;">
                            <tr>
                                <td>{{ model._id }}</td>
                                <td>{{ model.title }}</td>
                                <td>{{ model.objectTypeUuid }}</td>
                                <td>{{ model.description }}</td>
                                <td>{{ model.changedAt }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="type">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th style="width: 350px">Описание</th>
                                <th style="width: 130px">Дата изменения</th>
                            </tr>
                            </thead>
                            <tbody v-for="item in type" style="border: 1px solid #eee;">
                            <tr>
                                <td>{{ item._id }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.changedAt }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </header>
        </div>
    </div>
</div>

<?php
$vue = "
        var models = '" . json_encode($model) . "';
            models = JSON.parse(models);

        var type   = '" . json_encode($type) . "';
            type   = JSON.parse(type);

        new Vue({
            el: '#list-objects',
            data: {
                models: models,
                type: type,
            }
        });";
$this->registerJs($vue);
?>
