<?php
/**
 * Application configuration shared by all test types
 */
return [
    'language' => 'en-US',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/fixtures',
            'templatePath' => '@tests/codeception/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('TEST_DB_DSN'),
            'username' => getenv('TEST_DB_USERNAME'),
            'password' => getenv('TEST_DB_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'phactory' => 'perspectiva\phactory\Component',
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
