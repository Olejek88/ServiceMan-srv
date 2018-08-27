<?php

use common\components\MyHelpers;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $messagesNewCount */
/* @var $currentUser /console/model/Users */
/* @var $messagesIncome */
/* @var $messages */
/* @var $events_near */
/* @var $events_all */
/* @var $events_warning */
/* @var $content string */

$currentUser = Yii::$app->view->params['currentUser'];
$messages = Yii::$app->view->params['messagesIncome'];
$messagesNewCount = Yii::$app->view->params['messagesNewCount'];

$events_all = Yii::$app->view->params['events_all'];
$events_warning = Yii::$app->view->params['events_warning'];
$events_near = Yii::$app->view->params['events_near'];

$service_stopped = Yii::$app->view->params['service_stopped'];
$service_running = Yii::$app->view->params['service_running'];

$userImage = Yii::$app->view->params['userImage'];
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">T</span><span class="logo-lg">' . Yii::$app->name = 'ТОИРУС' . '</span>',
        Yii::$app->homeUrl, ['class' => 'logo']) ?>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="padding: 10px 15px">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu" style="padding-top: 0; padding-bottom: 0">
            <ul class="nav navbar-nav">
                <!-- Services -->
                <li class="tasks-menu">
                    <a href="/service" class="dropdown-toggle">
                        <i class="fa fa-clock-o"></i>
                        <?php
                        if ($service_stopped>0)
                            echo '<span class="label label-danger">'.$service_stopped.'</span>';
                        else
                            echo '<span class="label label-success">'.$service_running.'</span>';
                        ?>
                    </a>
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="tasks-menu">
                    <a href="/site/timeline" class="dropdown-toggle">
                        <i class="fa fa-flag-o"></i>
                        <span class="label label-info">0</span>
                    </a>
                </li>
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success"><?php if ($currentUser) echo $messagesNewCount ?></span>
                    </a>
                    <ul class="dropdown-menu" style="width: 350px">
                        <li class="header">
                            <?php
                            if ($currentUser && $messagesNewCount) echo 'У Вас '.$messagesNewCount.' новых сообщения';
                            ?>
                        </li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <?php
                                foreach ($messages as $message) {
                                    if ($message->fromUser) {
                                        $sender = $message->fromUser->name;
                                        $tmpPath = '/' . $message->fromUser->uuid . '/' . $message->fromUser->image;
                                        $path = MyHelpers::getImgUrl($tmpPath);
                                        if (!file_exists($path)) $path='/images/unknown.png';
                                    }
                                    else {
                                        $sender = 'неизвестен';
                                        $path = '/images/unknown.png';
                                    }
                                    echo '<li><a href="#"><div class="pull-left">';
                                    echo '<img src="'.$path.'" class="img-circle" alt="User Image">
                                          </div><h4>'.$currentUser['name'].'
                                          <small><i class="fa fa-clock-o"></i> '.$message['date'].'</small>
                                          </h4>
                                          <p style="white-space: normal">От: '.$sender.' <br/>'.$message['text'].'</p>';
                                     echo '</a></li>';
                                    }
                                ?>
                                <!-- end message -->
                            </ul>
                        </li>
                        <li class="footer"><a href="/message/list">Все сообщения</a></li>
                    </ul>
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning"><?php echo count($events_warning); ?></span>
                    </a>
                    <ul class="dropdown-menu" style="width: 400px">
                        <li class="header">У вас <?php echo count($events_near)+count($events_warning); ?>
                            событий в ближайшее время</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <?php
                                foreach ($events_all as $event) {
                                    echo '<li style="line-height: 1.3; padding: 0 5px 0 0">
                                          <a href="/event/view?id='.$event['_id'].'">
                                          <small><i class="fa fa-clock-o"></i> '.$event['next_date'].'</small>
                                          <p style="white-space: normal">
                                          <i class="fa fa-users text-aqua" style="margin: 3px"></i>'.$event['name'];
                                    echo '</p></a></li>';
                                }
                                ?>
                                <!-- end message -->
                            </ul>
                        </li>
                        <li class="footer"><a href="/event">Все события</a></li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php
                            echo '<img src="'.$userImage.'" class="user-image" alt="User Image">';
                        ?>
                        <span class="hidden-xs">
                            <?php
                                if ($currentUser) echo $currentUser['name'];
                            ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <?php
                            //$tmpPath = '/' . $currentUser['uuid'] . '/' . $currentUser['image'];
                            //$path = $currentUser->getImageUrl();
                            echo '<img src="'.$userImage.'" class="img-circle" alt="User Image">';
                            ?>
                            <p>
                                <?php
                                    if ($currentUser) echo $currentUser['name'].' - '.$currentUser['whoIs'];
                                    if ($currentUser) echo '<small>моб.тел.'.$currentUser['contact'].'</small>';
                                ?>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a('Профиль', ['users/view', 'id' => $currentUser['_id']],
                                    ['class' => 'btn btn-default btn-flat']) ?>
                            </div>
                            <div class="pull-right">
                            <?= $menuItems[] = Html::beginForm(['/logout'], 'post')
                                . Html::submitButton(
                                    'Выйти',
                                    [
                                        'class'       => 'btn btn-default btn-flat',
                                        'style'       => 'padding: 6px 16px 6px 16px;'
                                    ]
                                )
                                . Html::endForm();
                            ?>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>

</header>
