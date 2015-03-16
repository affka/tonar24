<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name' => 'ООО «Красноярск Тонар Сервис»',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'index/index',
    'language' => 'ru',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'asdadasdasdasdasd',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        /*'mailer' => [
            'class' => 'bryglen\sendgrid\Mailer',
            'username' => 'affka',
            'password' => 'TlGdWjnsrjckYKVRyTJMVfPISL4c9nQg',
            'viewPath' => '@app/views/mail',
        ],*/
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'index/index',
                'catalog/<slug>' => 'catalog/view',
                'product/<slug>' => 'product/view',
                'product/<slug>/order' => 'product/order',
                'product/<slug>/lease-form' => 'product/lease-form',
                '<action:(contact|service-map)>' => 'index/<action>',
                '<controller:\w+>/<id:\w+>' => '<controller>',
                '<controller:\w+>/<action:\w+>/<id:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    //$config['bootstrap'][] = 'debug';
    //$config['modules']['debug'] = 'yii\debug\Module';

    //$config['bootstrap'][] = 'gii';
    //$config['modules']['gii'] = 'yii\gii\Module';
}

// Append custom config
$customConfigPath = __DIR__ . '/../config.php';
if (file_exists($customConfigPath)) {
    $config = \yii\helpers\ArrayHelper::merge($config, require $customConfigPath);
}

return $config;
