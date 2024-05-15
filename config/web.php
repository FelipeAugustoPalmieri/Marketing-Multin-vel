<?php

use kartik\mpdf\Pdf;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'devicedetect'],
    'language' => 'pt-BR',
    'sourceLanguage' => 'en-US',
    'modules' => [
        'api' => 'app\api\Module',
    ],
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd/MM/yyyy',
            'datetimeFormat' => 'dd/MM/yyyy HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'R$ ',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            'cookieValidationKey' => getenv('COOKIES_KEY'),
        ],
        'cache' => [
            'class' => 'yii\caching\ArrayCache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'database' => getenv('REDIS_DATABASE'),
            //'password' => getenv('REDIS_PASSWORD'),
        ],
        'session' => [
            'class' => 'yii\web\Session',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => require(__DIR__ . '/i18n.php'),
        'mailer' => require(__DIR__ . '/mailer.php'),
        'pdf' => [
           'class' => Pdf::classname(),
           'format' => Pdf::FORMAT_A4,
           'orientation' => Pdf::ORIENT_PORTRAIT,
           'destination' => Pdf::DEST_BROWSER,
           'options' => ['debug' => true],
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
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST <controller:[\w-]+>' => '<controller>/asaas',
            ],
        ],
        'devicedetect' => [
           'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'enableI18N' => true,
                'messageCategory' => 'app',
            ],
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'enableI18N' => true,
                'messageCategory' => 'app',
            ],
            'form' => [
                'class' => 'yii\gii\generators\form\Generator',
                'enableI18N' => true,
                'messageCategory' => 'app',
            ],
        ],
    ];
}

return $config;
