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
        'migrate' => [
            'class' => 'console\controllers\MigrateController',
            'migrationPath' => '@app/migrations/schema',
            'migrationTable' => '{{%migration}}',
        ],
        'migrate-rbac' => [
            'class' => 'console\controllers\RbacMigrateController',
            'migrationPath' => '@app/migrations/rbac',
            'migrationTable' => '{{%migration_rbac}}',
        ],
        'migrate-content' => [
            'class' => 'console\controllers\ContentMigrateController',
            'migrationPath' => '@app/migrations/content',
            'migrationTable' => '{{%migration_content}}',
        ],
    ],

    'params' => $params,
];
