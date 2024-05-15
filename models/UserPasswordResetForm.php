<?php

namespace app\models;

use Yii;
use yii\base\Model;

class UserPasswordResetForm extends Model
{
    public $newPassword;
    public $newPasswordConfirmation;

    private $token;
    private $user;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
        if ($token) {
            $this->user = User::find()->where(['reset_password_token' => $token])->one();
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['newPassword', 'newPasswordConfirmation'], 'required'],
            ['newPasswordConfirmation', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'newPassword' => Yii::t('app', 'New Password'),
            'newPasswordConfirmation' => Yii::t('app', 'Repeat New Password'),
        ];
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->isValidToken() && $this->validate()) {
            $user = $this->user;
            $user->password = $this->newPassword;
            $user->reset_password_token = null;
            return (bool) $user->update(false, ['encrypted_password', 'reset_password_token']);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isValidToken()
    {
        return ($this->user instanceof User);
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->user ? $this->user->login : null;
    }
}
