<?php

namespace app\models;

use Yii;
use yii\base\Model;

class UserPasswordChangeForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;

    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['currentPassword', 'newPassword', 'newPasswordConfirmation'], 'required'],
            ['newPasswordConfirmation', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'currentPassword' => Yii::t('app', 'Current Password'),
            'newPassword' => Yii::t('app', 'New Password'),
            'newPasswordConfirmation' => Yii::t('app', 'Repeat New Password'),
        ];
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            if ($this->user->validatePassword($this->currentPassword)) {
                return $this->savePassword();
            }
            $this->addError('currentPassword', Yii::t('app/error', 'Current password is invalid.'));
        }
        return false;
    }

    public function savePassword(){
        $this->user->password = $this->newPassword;
        return $this->user->save();
    }

    public function generatePassword($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;
        if ($simbolos) $caracteres .= $simb;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }
}
