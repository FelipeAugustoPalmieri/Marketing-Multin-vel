<?php

namespace tests\codeception\unit\models;

use app\models\LoginForm;
use Codeception\Specify;
use Yii;
use perspectiva\phactory\Test;
use Phactory;

class LoginFormTest extends Test
{
    use Specify;

    protected function tearDown()
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testLoginWithNonexistantUser()
    {
        $model = new LoginForm([
            'username' => 'naoexistente',
            'password' => 'naoexistente',
        ]);

        expect('model should not login user', $model->login())->false();
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    public function testLoginWithWrongPassword()
    {
        $user = Phactory::user('business', ['login' => 'jacky']);
        $model = new LoginForm([
            'username' => 'jacky',
            'password' => 'senha_errada',
        ]);

        expect('model should not login user', $model->login())->false();
        expect('error message should be set', $model->errors)->hasKey('password');
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    public function testCorrectLogin()
    {
        $user = Phactory::user('business', ['login' => 'dany']);
        $model = new LoginForm([
            'username' => 'dany',
            'password' => 'senha',
        ]);

        expect('model should login user', $model->login())->true();
        expect('error message should not be set', $model->errors)->hasntKey('password');
        expect('user should be logged in', Yii::$app->user->isGuest)->false();
    }

}
