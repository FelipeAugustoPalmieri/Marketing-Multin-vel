<?php
namespace app\commands;

use Phactory;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class UsersController extends Controller
{
    public function actionCreate($login, $password, $email = null, $name = null)
    {
        $this->stdout("Creating admin user...");

        Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

        $params = [
            'login' => $login,
            'encrypted_password' => password_hash($password, PASSWORD_BCRYPT),
            'authenticable_type' => null,
            'authenticable_id' => null,
        ];

        if ($email) {
            $params['email'] = $email;
        }

        if ($name) {
            $params['name'] = $name;
        }

        $user = Phactory::user($params);

        Yii::$app->authManager->assign(
            Yii::$app->authManager->getRole('admin'),
            $user->getId()
        );

        $this->stdout(" OK\n", Console::FG_GREEN);
    }

    public function actionCreateEmployee($login, $password, $email = null, $name = null)
    {
        $this->stdout("Creating employee user...");

        Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

        $params = [
            'login' => $login,
            'encrypted_password' => password_hash($password, PASSWORD_BCRYPT),
            'authenticable_type' => null,
            'authenticable_id' => null,
        ];

        if ($email) {
            $params['email'] = $email;
        }

        if ($name) {
            $params['name'] = $name;
        }

        $user = Phactory::user($params);

        Yii::$app->authManager->assign(
            Yii::$app->authManager->getRole('employee'),
            $user->getId()
        );

        $this->stdout(" OK\n", Console::FG_GREEN);
    }
}
