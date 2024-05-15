<?php

namespace app\models;

use app\models\Consumer;
use app\models\Contratos;
use Yii;
use yii\base\Model;
use kartik\mpdf\Pdf;

class ConsumerUserForm extends Model
{
    public $authManager;
    public $consumer;

    public $id;
    public $name;
    public $email;
    public $identifier;
    public $password;
    public $passwordConfirmation;
    public $pathCaminho = 'terms/termo-cadastro-consumidor-tbest.pdf';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'identifier'], 'required'],
            [['password', 'passwordConfirmation'], 'required', 'on' => 'insert'],
            [['name', 'email', 'password', 'passwordConfirmation'], 'string', 'max' => 255],
            ['passwordConfirmation', 'compare', 'compareAttribute' => 'password'],
            [['identifier'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'insert' => ['name', 'email', 'password', 'passwordConfirmation', 'identifier'],
            'update' => ['name', 'email', 'password', 'passwordConfirmation', 'identifier'],
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
            'password' => Yii::t('app', 'Password'),
            'passwordConfirmation' => Yii::t('app', 'Password Confirmation'),
            'identifier' => Yii::t('app', 'Identifier'),
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
            $user->login = (string) $this->identifier;
            $user->email = $this->email;

            if ($this->password) {
                $user->password = $this->password;
            }

            if ($user->save()) {
                if ($this->scenario == 'insert') {
                    $role = $this->authManager->getRole('consumer');
                    $this->authManager->assign($role, $user->getId());

                    //$this->sendWelcomeEmail($user);
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
        if ($user = User::findOne(['id' => $id, 'authenticable_type' => 'Consumer'])) {
            $self = new static;
            $self->id = $id;
            $self->name = $user->name;
            $self->email = $user->email;
            $self->identifier = $user->login;
            $self->consumer = Consumer::findOne($user->authenticable_id);
            return $self;
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if ($this->id) {
            return User::findOne(['id' => $this->id, 'authenticable_type' => 'Consumer']);
        }
        $user = new User;
        $user->authenticable_type = 'Consumer';
        $user->authenticable_id = $this->consumer->id;

        return $user;
    }

    /**
     * @param User $user
     * @return boolean
     */
    protected function sendWelcomeEmail(User $user)
    {
        $this->gerarTermoCadastro();
        $mailer = Yii::$app->mailer->compose(
            'user/welcome-consumer',
            [
                'name' => $user->name,
                'login' => $user->login,
                'password' => $this->password,
            ]
        )
        ->setFrom(getenv('MAILER_FROM'))
        ->setTo($user->email)
        ->setSubject(Yii::t('app/mail', 'Welcome to TBest System!'));
        if(file_exists($this->pathCaminho))
            $mailer->attach($this->pathCaminho);
        
        $mailer->send();

        if(file_exists($this->pathCaminho))
            unlink($this->pathCaminho);

        return true;
    }

    private function gerarTermoCadastro(){
        $contratos = new Contratos();
        $contratos->tituloTermos = $this->pathCaminho;
        $mpdf = $contratos->buscarTermosCadastro(false);
        $mpdf->destination = Pdf::DEST_FILE;
        $mpdf->render();
    }
}
