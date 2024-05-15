<?php
$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->overload();

try {
    $dotenv->required([
        'COOKIES_KEY', 'DB_DSN', 'DB_USERNAME',
        'REDIS_HOST', 'REDIS_DATABASE', 'REDIS_PORT',
        'MAILER_FROM', 'MAILER_DRIVER', 'MAILER_HOST', 'MAILER_USERNAME', 'MAILER_PORT', 'MAILER_ENCRYPTION',
    ])->notEmpty();

    // É obrigatória, mas pode estar vazia: ""
    $dotenv->required([
        'DB_PASSWORD',
        'REDIS_PASSWORD',
        'MAILER_PASSWORD',
    ]);

    $dotenv->required('ENVIRONMENT')->allowedValues(['development', 'test', 'production']);

} catch (Exception $e) {
    echo "Verifique o arquivo \".env\":\n";
    echo $e->getMessage(), "\n";
    exit(1);
}
