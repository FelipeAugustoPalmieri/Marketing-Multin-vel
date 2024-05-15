<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "legal_persons".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $person_class
 * @property integer $person_id
 * @property string $address
 * @property string $district
 * @property integer $city_id
 * @property string $zip_code
 * @property string $cell_number
 * @property string $email
 * @property string $website
 * @property string $address_complement
 *
 * @property City $city
 */
class LegalPerson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'legal_persons';
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
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['person_id'], 'integer'],
            [['person_class'], 'in', 'range' => ['PhysicalPerson', 'JuridicalPerson']],
            [
                ['person_class', 'person_id'],
                'unique',
                'targetAttribute' => ['person_class', 'person_id'],
                'message' => Yii::t('app/error', 'This legal person already is marked as a business.')
            ],
            [['person_class', 'person_id', 'address', 'district', 'city_id', 'zip_code', 'cell_number', 'email'], 'required'],
            [['person_id', 'city_id'], 'integer'],
            [['person_class', 'address', 'address_complement', 'district', 'zip_code', 'cell_number', 'home_phone','comercial_phone', 'email', 'website'], 'string', 'max' => 255],
            [['website'], 'url'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $safeAttributes = [
            'address', 'address_complement', 'district', 'city_id', 'zip_code', 'cell_number', 'home_phone','comercial_phone', 'email', 'website',
        ];
        return [
            'default' => array_merge($safeAttributes, ['person_class', 'person_id']),
            'insert' => array_merge($safeAttributes, ['person_class', 'person_id']),
            'update' => array_merge($safeAttributes, ['person_class', 'person_id']),
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
            'person_class' => Yii::t('app', 'Person Type'),
            'person_id' => Yii::t('app', 'Type ID'),
            'address' => Yii::t('app', 'Address'),
            'district' => Yii::t('app', 'District'),
            'city_id' => Yii::t('app', 'City ID'),
            'zip_code' => Yii::t('app', 'Zipcode'),
            'cell_number' => Yii::t('app', 'Cell Phone'),
            'email' => Yii::t('app', 'Email'),
            'website' => Yii::t('app', 'Website'),
            'address_complement' => Yii::t('app', 'Address Complement'),
            'home_phone' => Yii::t('app', 'Home Phone'),
            'comercial_phone' => Yii::t('app', 'Comercial Phone'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Polymorphic relationship with PhysicalPerson or JuridicalPerson,
     * using $person_class (class) and $person_id (ID).
     * @return yii\db\ActiveQuery
     */
    public function getPerson()
    {
        if ($this->person_class == 'JuridicalPerson') {
            return $this->hasOne(JuridicalPerson::className(), ['id' => 'person_id']);
        } elseif ($this->person_class == 'PhysicalPerson') {
            return $this->hasOne(PhysicalPerson::className(), ['id' => 'person_id']);
        }
    }

    /**
     * @return array onde a chave é o name da classe e o valor a descrição legível.
     */
    public static function getTypes()
    {
        return [
            'JuridicalPerson' => Yii::t('app', 'Juridical Person'),
            'PhysicalPerson' => Yii::t('app', 'Physical Person'),
        ];
    }

    /**
     * @return string descrição legível do person de legalPerson
     */
    public function getType()
    {
        if (!empty(self::getTypes()[$this->person_class])) {
            return self::getTypes()[$this->person_class];
        }
    }

    /**
     * @return string name fantasia ou name completo da legalPerson relacionada
     */
    public function getName()
    {
        $person = $this->person;

        if ($person instanceof JuridicalPerson) {
            return $person->trading_name;
        }

        if ($person instanceof PhysicalPerson) {
            return $person->name;
        }
    }

    public function getPhoneNumber()
    {
        return $this->cell_number;
    }

    /**
     * @return string CPF ou CNPJ
     */
    public function getNationalIdentifier()
    {
        $person = $this->person;

        if ($person instanceof JuridicalPerson) {
            return $person->cnpj;
        }

        if ($person instanceof PhysicalPerson) {
            return $person->cpf;
        }
    }

    /**
     * @return boolean
     */
    public function isPhysicalPerson()
    {
        return $this->person_class == 'PhysicalPerson';
    }

    /**
     * @return boolean
     */
    public function isJuridicalPerson()
    {
        return $this->person_class == 'JuridicalPerson';
    }
}
