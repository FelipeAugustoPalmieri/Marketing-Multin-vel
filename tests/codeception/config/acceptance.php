<?php
/**
 * Application configuration for acceptance tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/web.php'),
    require(__DIR__ . '/config.php'),
    [
        'components' => [
            'request' => [
                'enableCsrfValidation' => false,
                'cookieValidationKey' => getenv('COOKIES_KEY'),
            ],
        ],
    ]
);
