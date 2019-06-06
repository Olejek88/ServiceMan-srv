<?php

use kartik\datecontrol\Module;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => '',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log',
        'queue'
    ],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd-MM-yyyy',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss a',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

        ]],
    'components' => [
        'telegram' => [
            'class' => 'aki\telegram\Telegram',
            'botToken' => '489333537:AAFWzSpAuWl0v1KJ3sTQKYABpjY0ERgcIcY',
        ],
        'queue' => [
            'class' => \yii\queue\amqp_interop\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'queueName' => 'queue',
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@backend/views/yii2-app'
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/dashboard',
                '/timeline' => 'site/timeline',
                '/login' => 'site/login',
                '/logout' => 'site/logout',
                '/signup' => 'site/signup',
                '<controller>/<id:\d+>' => '<controller>/create',
                '<controller>/<id:\d+>' => '<controller>/delete',
                '<controller>/<id:\d+>' => '<controller>/view',
                '<controller>/<id:\d+>' => '<controller>/info',
            ],
        ],

    ],
    'params' => $params,
];
