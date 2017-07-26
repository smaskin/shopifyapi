<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'modules' => [
        'storage' => [
            'class' => 'app\modules\storage\Module',
            'apiKey' => '71b99c41698a83f4f6059feea8ca5223',
            'sharedSecret' => '6c9086eeeba452e1a52a41906ec3c812',
            'scopes' => 'read_products',
            'appName' => 'backup-storage',
            'webhookUrls' => [
                'app/uninstalled' => '/storage/hook/uninstall',
                'products/create' => '/storage/product/hook/update',
                'products/update' => '/storage/product/hook/update',
            ]
        ]
    ],
    'components' => [
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'request' => [
            'cookieValidationKey' => '7bI68XlgU_zXXKMErdrpiwJMkwM02J3s',
            'enableCsrfValidation' => false
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\storage\models\Shop',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '90.154.65.1']

    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '90.154.65.1']
    ];
}

return $config;
