<?php

namespace app\models;

use app\models\Business;
use Yii;
use yii\base\Model;

class BusinessUserForm extends Model
{
    public $authManager;
    public $business;

    public $id;
    public $name;
    public $email;
    public $login;
    public $password;
    public $passwordConfirmation;
    public $canSeeSalesReport = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'login'], 'required'],
            [['password', 'passwordConfirmation', 'login'], 'required', 'on' => 'insert'],
            [['name', 'email', 'password', 'passwordConfirmation', 'login'], 'string', 'max' => 255],
            ['canSeeSalesReport', 'boolean'],
            ['passwordConfirmation', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'insert' => ['name', 'email', 'login', 'password', 'passwordConfirmation', 'canSeeSalesReport'],
            'update' => ['name', 'email', 'login', 'password', 'passwordConfirmation', 'canSeeSalesReport'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'login' => Yii::t('app', 'Login'),
            'password' => Yii::t('app', 'Password'),
            'passwordConfirmation' => Yii::t('app', 'Password Confirmation'),
            'canSeeSalesReport' => Yii::t('app', 'Can see sales report'),
        ];
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->name = $this->name;
            $user->login = $this->login;
            $user->email = $this->email;

            if ($this->password) {
                $user->password = $this->password;
            }

            if ($user->save()) {
                $permission = $this->authManager->getPermission('salesReport');
                $this->authManager->revoke($permission, $user->getId());

                if ($this->scenario == 'insert') {
                    $role = $this->authManager->getRole('business');
                    $this->authManager->assign($role, $user->getId());

                    $this->sendWelcomeEmail($user);
                }
                if ($this->canSeeSalesReport) {
                    $this->authManager->assign($permission, $user->getId());
                }

                return true;
            }

            foreach ($user->getErrors() as $attribute => $errors) {
                if (!$this->hasProperty($attribute)) {
                    $attribute = 'email';
                }
                $this->addErrors([$attribute => $errors]);
            }
        }
        return false;
    }

    /**
     * @return integer affected rows
     */
    public function delete()
    {
        return $this->getUser()->delete();
    }

    /**
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->getUser()->getIsNewRecord();
    }

    /**
     * @return static
     */
    public static function findOne($id)
    {
        if ($user = User::findOne(['id' => $id, 'authenticable_type' => 'Business'])) {
            $self = new static;
            $self->id = $id;
            $self->name = $user->name;
            $self->email = $user->email;
            $self->login = $user->login;
            $self->business = Business::findOne($user->authenticable_id);
            return $self;
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if ($this->id) {
            return User::findOne(['id' => $this->id, 'authenticable_type' => 'Business']);
        }
        $user = new User;
        $user->authenticable_type = 'Business';
        $user->authenticable_id = $this->business->id;

        return $user;
    }

    /**
     * @param User $user
     * @return boolean
     */
    protected function sendWelcomeEmail(User $user)
    {
        return Yii::$app->mailer->compose(
                'user/welcome-business',
                [
                    'name' => $user->name,
                    'login' => $user->login,
                    'password' => $this->password,
                ]
            )
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo($user->email)
            ->setSubject(Yii::t('app/mail', 'Welcome to TBest System!'))
            ->send()
        ;
    }
}
