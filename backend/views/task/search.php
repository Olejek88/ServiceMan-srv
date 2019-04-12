<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Orders */

// $this->title = $model->title;

// return var_dump($model);

?>

<div class="orders-view box-padding">

    <div class="panel panel-default">

        <h3 class="text-center" style="padding: 20px 5px 0px 5px;">Поиск задачи</h3>

        <div class="input-group" style="width: 500px; margin: 0 auto;">
            <input type="text" class="form-control" placeholder="Введите идентификатор задачи">
            <span class="input-group-btn">
            <button class="btn btn-secondary" type="button">Найти</button>
            </span>
        </div>
        <div class="panel-body">
            <header class="header-result">

                <ul class="nav nav-tabs" style="width: 200px; margin: 0 auto;">
                    <li class="active"><a href="#help" data-toggle="tab">Помощь</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Параметры <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li role="presentation" class="dropdown-header">Фильтры</li>
                            <li><a href="#time" data-toggle="tab">Время</a></li>
                            <li><a href="#location" data-toggle="tab">Место</a></li>
                        </ul>
                    </li>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="help">
                        <h6 class="text-center">
                            Данный раздел находится в разработке..
                        </h6>
                    </div>
                    <div class="tab-pane fade" id="time">
                        <h6 class="text-center">
                            Данный раздел находится в разработке..
                        </h6>
                    </div>
                    <div class="tab-pane fade" id="location">
                        <h6 class="text-center">
                            Данный раздел находится в разработке..
                        </h6>
                    </div>
                </div>

            </header>

        </div>
</div>

</div>
