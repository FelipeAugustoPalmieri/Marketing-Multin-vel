<?php

namespace tests\codeception\_pages;

use Phactory;
use Yii;
use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class LoginPage extends BasePage
{
    public $route = 'site/login';

    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $this->actor->fillField('input[name="LoginForm[username]"]', $username);
        $this->actor->fillField('input[name="LoginForm[password]"]', $password);
        $this->actor->click('login-button');
    }

    public static function signInAsAdmin($I)
    {
        $loginPage = self::openBy($I);

        $user = Phactory::user([
            'login' => 'admin',
            'encrypted_password' => password_hash('admin', PASSWORD_BCRYPT),
            'authenticable_type' => null,
            'authenticable_id' => null,
        ]);

        Yii::$app->authManager->assign(
            Yii::$app->authManager->getRole('admin'),
            $user->getId()
        );

        $loginPage->login('admin', 'admin');
    }

    public static function signInAsConsumer($I)
    {
        $loginPage = self::openBy($I);

        $plane = Phactory::plane(['multiplier' => 0.6]);

        $consumer = Phactory::consumer(['plane_id' => $plane->id]);

        $user = Phactory::user([
            'login' => 'consumer',
            'encrypted_password' => password_hash('consumer', PASSWORD_BCRYPT),
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $consumer->id,
        ]);

        Yii::$app->authManager->assign(
            Yii::$app->authManager->getRole('consumer'),
            $user->getId()
        );

        $loginPage->login('consumer', 'consumer');

        return $consumer;
    }
}
