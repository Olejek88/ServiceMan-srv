<?php$currentUser = Yii::$app->view->params['currentUser'];$userImage = Yii::$app->view->params['userImage'];?><aside class="main-sidebar">    <section class="sidebar">        <!-- Sidebar user panel -->        <div class="user-panel">            <div class="pull-left image">                <?php                    echo '<img src="'.$userImage.'" class="img-circle" alt="User Image">';                ?>            </div>            <div class="pull-left info">                <p><?php  if ($currentUser) echo $currentUser['name']; ?> </p>                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>            </div>        </div>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu'],                'items' => [                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],                    [                        'label' => 'Карта',                        'icon' => 'fa fa-map',                        'url' => '/site/index',                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    [                        "label" => "Оборудование",                        'icon' => 'glyphicon glyphicon-inbox',                        "items" => [                            ["label" => "Таблицей", "url" => ["/equipment"]],                            ["label" => "Деревом", "url" => ["/equipment/tree"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        'label' => 'Аварии',                        'icon' => 'glyphicon glyphicon-book',                        'url' => '/alarm',                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        'label' => 'Сообщения',                        'icon' => 'glyphicon glyphicon-pencil',                        'url' => '/message',                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        'label' => 'Измерения',                        'icon' => 'glyphicon glyphicon-stats',                        'url' => '/measure',                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    [                        "label" => "Абоненты",                        'icon' => 'glyphicon glyphicon-equalizer',                        "items" => [                            ["label" => "Таблицей", "url" => ["/residents/table"]],                            ["label" => "Деревом", "url" => ["/residents/tree"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    [                        'label' => 'Субъекты',                        'icon' => 'glyphicon glyphicon-book',                        'url' => '/subject/table',                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    [                        "label" => "Контроллеры",                        'icon' => 'glyphicon glyphicon-user',                        "items" => [                            ["label" => "Пользователи", "url" => ["/users"]],                            ["label" => "Дашбоард", "url" => ["/users/dashboard"]],                        ],                    ],                ],            ]        ) ?>    </section></aside>