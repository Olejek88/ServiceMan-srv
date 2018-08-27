<?php

use yii\helpers\Html;

/* @var $model \common\models\Users */
/* @var $user_property */
/* @var $orders */
/* @var $events */
/* @var $tree */

$this->title = 'Профиль пользователя :: '.$model->name;
?>
<style>
.btn {
    text-transform: none;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
             Профиль пользователя
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i> Главная</a></li>
            <li class="active"><a href="/users/dashboard">Пользователи</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <?php
                        $path = $model->getImageUrl();
                        if (!$path || !$model['image']) {
                            $path='/images/unknown2.png';
                        }
                        echo '<img class="profile-user-img img-responsive img-circle" src="'.Html::encode($path).'">';
                        ?>
                        <h3 class="profile-username text-center"><?php echo $model['name'] ?></h3>
                        <p class="text-muted text-center"><?php echo $model['whoIs'] ?></p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Нарядов</b> <a class="pull-right"><?php echo $user_property['orders'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Дефектов</b> <a class="pull-right"><?php echo $user_property['defects'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Собщений</b> <a class="pull-right"><?php echo $user_property['messages'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Аттрибутов</b> <a class="pull-right"><?php echo $user_property['attributes'] ?></a>
                            </li>
                        </ul>
<!--                        <a href="#" class="btn btn-primary btn-block"><b>Аттрибуты</b></a> -->
                    </div>
                </div>

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Информация</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <strong><i class="fa fa-mobile margin-r-5"></i> Контакт</strong>
                        <span class="text-muted">
                            <?php echo $model['contact'] ?>
                        </span>
                        <hr>
                        <strong><i class="fa fa-tag margin-r-5"></i> Тег</strong>
                        <span class="text-muted">
                            <?php echo $model['tagId'] ?>
                        </span>
                        <hr>
                        <strong><i class="fa fa-map-marker margin-r-5"></i> Координаты</strong>
                        <p class="text-muted">
                            <?php echo $user_property['location'] ?>
                        </p>
                        <hr>

                        <strong><i class="fa fa-check-circle margin-r-5"></i> Статус</strong>
                            <?php
                             if ($model['active']) echo '<span class="label label-success">Активен</span>';
                             else echo '<span class="label label-danger">Не активен</span>';
                            ?>

                        <hr>
                        <strong><i class="fa fa-pencil margin-r-5"></i> Специализация</strong>
                        <p>
                            <span class="label label-danger">Администратор</span>
                            <span class="label label-success">Оператор</span>
                            <span class="label label-info">Персонал</span>
                            <span class="label label-warning">Техник</span>
                        </p>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-right: 0px"><a href="#timeline" data-toggle="tab">Журнал</a></li>
                        <li style="margin-right: 0px"><a href="#activity" data-toggle="tab">Активность</a></li>
                        <li style="margin-right: 0px"><a href="#settings" data-toggle="tab">Настройки</a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="active tab-pane" id="timeline">
                            <!-- The timeline -->
                            <ul class="timeline timeline-inverse">
                                <?php
                                foreach ($events as $event) {
                                    echo $event['event'];
                                }
                                ?>
                            </ul>
                        </div>

                        <div class="tab-pane" id="activity">
                            <!-- Post -->
                            <?php
                                $orderCount=0;
                                foreach ($orders as $order) {
                                    echo '<div class="post"><div class="user-block">';
                                    $path = $order['author']->getImageUrl();
                                    if (!$path || !$order['author']['image']) {
                                        $path='/images/unknown.png';
                                    }
                                    echo '<img class="img-circle img-bordered-sm" src="'.Html::encode($path).'">';
                                    if ($order['startDate']>0) $startDate = date("M j, Y", strtotime($order['startDate']));
                                    else $startDate = 'не назначен';
                                    if ($order['openDate']>0) $openDate = date("M j, Y", strtotime($order['openDate']));
                                    else $openDate = 'не начинался';
                                    if ($order['closeDate']>0) $closeDate = date("M j, Y", strtotime($order['openDate']));
                                    else $closeDate = 'не закончился';

                                    echo '<span class="username">
                                          <a href="#">'.$order['title'].'</a>
                                          <a href="#" class="pull-right btn-box-tool"><i class="fa fa-time"></i></a>
                                          </span>
                                          <span class="description">Назначен на '.$startDate.' ['.$openDate.' - '.$closeDate.']</span>
                                          </div>';
                                    echo '<p>'.$tree[$orderCount].'</p>';
                                    echo  '<ul class="list-inline">
                                            <li><a href="/orders/view?id='.$order["_id"].'" class="link-black text-sm"><i class="fa fa-share margin-r-5"></i> Редактировать</a></li>
                                            <li class="pull-right"><a href="#" class="link-black text-sm"><i class="fa fa-comments-o margin-r-5"></i> Сообщение по наряду</a></li>
                                            </ul>';
                                    echo '</div>';
                                    $orderCount++;
                                }
                            ?>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="settings">
                            <div class="post"><div class="user-block">
                            <?= $this->render('_form', [
                                'model' => $model,['class' => 'form-horizontal']
                            ]) ?>
                            </div></div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->