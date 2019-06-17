<?php$currentUser = Yii::$app->view->params['currentUser'];$userImage = Yii::$app->view->params['userImage'];use dmstr\widgets\Menu; ?><aside class="main-sidebar">    <section class="sidebar">        <!-- Sidebar user panel -->        <div class="user-panel">            <div class="pull-left image">                <?php                echo '<img src="' . $userImage . '" class="img-circle" alt="User Image">';                ?>            </div>            <div class="pull-left info">                <p><?php if ($currentUser) echo $currentUser['name']; ?> </p>                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>            </div>        </div>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu'],                'items' => [                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],                    [                        'label' => 'Карта',                        'icon' => 'fa fa-map',                        'url' => '/site/index',                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    [                        "label" => "Оборудование",                        'icon' => 'fa fa-tag',                        "items" => [                            ["label" => "Сводная", 'icon' => 'fa fa-table', "url" => ["/equipment"]],                            ["label" => "По типам оборудования", 'icon' => 'fa fa-tree', "url" => ["/equipment/tree"]],                            ["label" => "История работ", 'icon' => 'fa fa-history', "url" => ["/equipment/timeline-all"]],                            ["label" => "По местоположению", 'icon' => 'fa fa-tree', "url" => ["/equipment/tree-street"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        "label" => "Операторы",                        'icon' => 'fa fa-users',                        "items" => [                            ["label" => "Сводная", 'icon' => 'fa fa-user', "url" => ["/users/dashboard"]],                            ["label" => "Таблицей", 'icon' => 'fa fa-table', "url" => ["/users/table"]],                            ["label" => "История работ", 'icon' => 'fa fa-history', "url" => ["/users/timeline"]],                            ["label" => "Журнал системы", 'icon' => 'fa fa-history', "url" => ["site/timeline"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        "label" => "Документация",                        'icon' => 'fa fa-file',                        "items" => [                            ["label" => "Деревом", 'icon' => 'fa fa-tree', "url" => ["/site/files"]],                            ["label" => "Таблицей", 'icon' => 'fa fa-table', "url" => ["/documentation/index"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        "label" => "Объекты",                        'icon' => 'fa fa-home',                        "items" => [                            ["label" => "Деревом", 'icon' => 'fa fa-file', "url" => ["/object/tree"]],                            ["label" => "Таблицей", 'icon' => 'fa fa-table', "url" => ["/object/table"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        "label" => "Задачи",                        'icon' => 'fa fa-tasks',                        "items" => [                            ["label" => "План-график", 'icon' => 'fa fa-calendar', "url" => ["/task-template-equipment/calendar-gantt"]],                            ["label" => "Задачи", 'icon' => 'fa fa-user', "url" => ["task/table-user"]],                            ["label" => "Деревом", 'icon' => 'fa fa-tree', "url" => ["task/tree"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        "label" => "Конфигурация задач",                        'icon' => 'fa fa-cogs',                        "items" => [                            ["label" => "Технологические карты", 'icon' => 'fa fa-tree', "url" => ["task-template/tree"]],                            ["label" => "Типы задач", 'icon' => 'fa fa-table', "url" => ["/task-type"]],                            ["label" => "Шаблоны задач", 'icon' => 'fa fa-buffer', "url" => ["/task-template"]]                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu push-analytics', 'data-widget' => 'tree',],                'items' => [                    [                        "label" => "Отчеты",                        'icon' => 'fa fa-list-alt',                        "items" => [                            ["label" => "Отчет по исполнителям", 'icon' => 'fa fa-calendar', "url" => ["task/table-user"]],                            ["label" => "Нормативы", 'icon' => 'fa fa-table', "url" => ["task/table-user-normative"]],                            ["label" => "По пользователям", 'icon' => 'fa fa-user', "url" => ["task/table-user"]],                            ["label" => "Деревом", 'icon' => 'fa fa-tree', "url" => ["task/tree"]],                            ["label" => "Журнал осмотров", 'icon' => 'fa fa-table', "url" => ["task/table-report-view"]],                            ["label" => "Отчет о показаниях", 'icon' => 'fa fa-table', "url" => ["/measure"]]                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    [                        "label" => "Система",                        'icon' => 'fa fa-laptop',                        "items" => [                            ["label" => "Аварийные отключения", 'icon' => 'fa fa-table', "url" => ["shutdown/index"]],                            ["label" => "Журнал диспетчера", 'icon' => 'fa fa-table', "url" => ["/request/"]],                            ["label" => "Журнал приема", 'icon' => 'fa fa-table', "url" => ["/receipt/"]],                            ["label" => "Справочник контрагентов", 'icon' => 'fa fa-users', "url" => ["contragent/table"]],                            ["label" => "Дефекты", 'icon' => 'fa fa-table', "url" => ["/defect"]],                        ],                    ],                ],            ]        ) ?>    </section></aside>