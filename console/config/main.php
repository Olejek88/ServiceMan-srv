<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'export' => [
            'class' => 'console\controllers\ExportController',
        ],
        'daemon' => [
            'class' => 'inpassor\daemon\Controller',
            'uid' => 'daemon',              // The daemon UID. Giving daemons different UIDs makes possible to run several daemons.
            'pidDir' => '@console/runtime/daemon',
            'logsDir' => '@console/runtime/daemon/logs',
            'clearLogs' => false,                       // Clear log files on start.
            'workersMap' => [
                'task_service' => [
                    'class' => 'console\workers\TaskService',
                ],
            ],
        ],

    ],

    'params' => $params,
];
