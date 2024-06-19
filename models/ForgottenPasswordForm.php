<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ForgottenPasswordForm extends Model
{
    public $login;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['login'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => Yii::t('app', 'Login'),
        ];
    }

    /**
     * Send password reset instructions
     */
    public function save()
    {
        if ($this->validate()) {
            $user = User::find()->where(['login' => $this->login])->one();

            if ($user) {
                $user->generateResetPasswordToken();
                $user->update(['reset_password_token']);
                $this->sendInstructionsEmail($user);
                return true;
            }

            $this->addError('email', Yii::t('app/error', 'There is no account associated with your e-mail address.'));
        }
        return false;
    }

    /**
     * @param User $user
     * @return boolean
     */
    protected function sendInstructionsEmail(User $user)
    {
        return Yii::$app->mailer->compose(
                'user/reset-password',
                [
                    'name' => $user->name,
                    'login' => $user->login,
                    'token' => $user->reset_password_token,
                ]
            )
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo($user->email)
            ->setSubject(Yii::t('app/mail', 'Reset TBest Password'))
            ->send();
    }
}
