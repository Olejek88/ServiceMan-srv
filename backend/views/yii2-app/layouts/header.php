<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $currentUser /console/model/Users */
/* @var $content string */

$currentUser = Yii::$app->view->params['currentUser'];
$userImage = Yii::$app->view->params['userImage'];
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">С</span><span class="logo-lg">' . Yii::$app->name = 'СЕРВИС' . '</span>',
        Yii::$app->homeUrl, ['class' => 'logo']) ?>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="padding: 10px 15px">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu" style="padding-top: 0; padding-bottom: 0">
            <ul class="nav navbar-nav">
                <li class="tasks-menu">
                    <a href="/site/timeline" class="dropdown-toggle">
                        <i class="fa fa-flag-o"></i>
                        <span class="label label-info">0</span>
                    </a>
                </li>
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="/message/list" class="dropdown-toggle">
                        <i class="fa fa-envelope-o"></i>
                    </a>
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="/alarm" class="dropdown-toggle">
                        <i class="fa fa-bell-o"></i>
                    </a>
                </li>

                <li class="dropdown references-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cogs"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?= Html::a("<i class=\"fa fa-warning\"></i> Предупреждения статус", ['../alarm-status']); ?></li>
                        <li><?= Html::a("<i class=\"fa fa-warning\"></i> Предупреждения типы", ['../alarm-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-address-book\"></i> Документация типы", ['../documentation-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-user\"></i> Контрагенты типы", ['../contragent-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-book\"></i> Журнал типы записей", ['../equipment-register-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-tree\"></i> Статусы элементов", ['../equipment-status']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cog\"></i> Типы элементов", ['../equipment-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-steam\"></i> Иженерные системы", ['../equipment-system']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Типы домов", ['../house-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-object-group\"></i> Статусы объектов", ['../object-status']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Типы объектов", ['../object-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Статусы запросов", ['../request-status']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Характеры обращений", ['../request-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Типы задач", ['../task-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Вердикты задач", ['../task-verdict']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-cogs\"></i> Статусы работ", ['../work-status']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-bar-chart\"></i> Типы измерений", ['../measure-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-list\"></i> Типы дефектов", ['../defect-type']); ?> </li>
                        <li><?= Html::a("<i class=\"fa fa-tasks\"></i> Шаблоны задач", ['../task-template']); ?> </li>
                    </ul>
                </li>

                <li class="dropdown references-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-home"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?= Html::a("<i class=\"fa fa-home\"></i> Города", ['../city']); ?></li>
                        <li><?= Html::a("<i class=\"fa fa-street-view\"></i> Улицы", ['../street']); ?> </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php
                        echo '<img src="' . $userImage . '" class="user-image" alt="User Image">';
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
                            echo '<img src="' . $userImage . '" class="img-circle" alt="User Image">';
                            ?>
                            <p>
                                <?php
                                if ($currentUser) echo $currentUser['name'];
                                if ($currentUser) echo '<small>моб.тел.' . $currentUser['contact'] . '</small>';
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
                                            'class' => 'btn btn-default btn-flat',
                                            'style' => 'padding: 6px 16px 6px 16px;'
                                        ]
                                    )
                                    . Html::endForm();
                                ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

</header>
