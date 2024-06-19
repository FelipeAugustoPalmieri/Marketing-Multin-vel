<?php

namespace app\models;

use BadMethodCallException;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $login
 * @property string $email
 * @property string $encrypted_password
 * @property string $authenticable_type
 * @property integer $authenticable_id
 * @property string $reset_password_token
 *
 * @method static User|null findOne($condition)
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['!login', 'name', 'email', '!encrypted_password'], 'required'],
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['email'], 'email'],
            [['!authenticable_type'], 'in', 'range' => ['Admin', 'Business', 'Consumer'], 'skipOnEmpty' => true],
            [['!authenticable_id'], 'integer', 'skipOnEmpty' => true],
            [['login', 'email', '!encrypted_password'], 'string', 'max' => 255],
            [['login'], 'unique'],
            [['email'], 'unique'],
            [['!reset_password_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'name' => Yii::t('app', 'Name'),
            'login' => Yii::t('app', 'Login'),
            'email' => Yii::t('app', 'Email'),
            'encrypted_password' => Yii::t('app', 'Encrypted Password'),
            'authenticable_id' => Yii::t('app', 'Authenticable ID'),
            'authenticable_type' => Yii::t('app', 'Authenticable Type'),
            'password' => Yii::t('app', 'Password'),
            'reset_password_token' => Yii::t('app', 'Reset Password Token'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->encrypted_password;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->encrypted_password === $authKey;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->encrypted_password);
    }

    /**
     * @param string $value
     */
    public function setPassword($value)
    {
        $this->encrypted_password = password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * Generates a reset_password_token, assigns it to this object and return it.
     * @return string
     */
    public function generateResetPasswordToken()
    {
        return ($this->reset_password_token = uniqid(rand(), true));
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'authenticable_id']);
    }

    /**
     * @return boolean
     **/
    public function isActive()
    {
        if ($this->authenticable_type != 'Business') {
            return true;
        }

        $business = Business::find()->andWhere(['id' => $this->authenticable_id])->one();
        return !$business->is_disabled;
    }
}
