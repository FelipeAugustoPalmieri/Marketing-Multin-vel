<?php
return [
    'class' => 'yii\swiftmailer\Mailer',
    'transport' => [
        'class' => getenv('MAILER_DRIVER'),
        'host' => getenv('MAILER_HOST'),
        'username' => getenv('MAILER_USERNAME'),
        'password' => getenv('MAILER_PASSWORD'),
        'port' => getenv('MAILER_PORT'),
        'encryption' => getenv('MAILER_ENCRYPTION'),
    ],
];
