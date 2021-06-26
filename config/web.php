<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '5gxVvYoIxU1vyEOyguYpBPcS-3QBW42y',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                //api
                'POST /api/<controller:[\w-]+>' => 'api/<controller>/create',
                'DELETE /api/<controller:[\w-]+>' => 'api/<controller>/delete',
                'DELETE /api/<controller:[\w-]+>/<id:\d+>' => 'api/<controller>/delete',
                'PUT /api/<controller:[\w-]+>' => 'api/<controller>/update',
                'PUT /api/<controller:[\w-]+>/<id:\d+>' => 'api/<controller>/update',
                'PUT /api/<controller:[\w-]+>/<id:\d+>/<second:.+>' => 'api/<controller>/update',
                '/api/<controller:[\w-]+>' => 'api/<controller>/index',
                '/api/<controller:[\w-]+>/<action:[\w-]+>/<id:.+>/<arg1:.+>' => 'api/<controller>/<action>',
                '/api/<controller:[\w-]+>/<id:\w+>' => 'api/<controller>/view',
                '/api/<controller:[\w-]+>/<id:\d+>' => 'api/<controller>/view',
                '/api/<controller:[\w-]+>/<action:[\w-]+>' => 'api/<controller>/<action>',
                '/api/<controller:[\w-]+>/<action:[\w-]+>/<id:.+>' => 'api/<controller>/<action>',

                // site
                '' => 'site/site/index',
                'site' => 'site/site/index',
                '<action:\w+>' => 'site/site/<action>',
                '<action>' => 'site/site/<action>',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}

return $config;
