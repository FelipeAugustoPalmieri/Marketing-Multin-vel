<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yiibr\brvalidator\CpfValidator;

/**
 * This is the model class for table "physical_persons".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $cpf
 * @property string $rg
 * @property string $nationality
 * @property string $born_on
 * @property string $marital_status
 * @property string $partner_name
 * @property string $partner_born_on
 * @property string $partner_phone_number
 * @property string $partner_cpf
 * @property string $partner_rg
 * @property integer $occupation_id
 * @property string $issuing_body
 *
 * @property Occupations $occupation
 */
class PhysicalPerson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'physical_persons';
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
            [['name', 'cpf'], 'required'],
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['cpf'], CpfValidator::className()],
            [['occupation_id'], 'integer'],
            [['name', 'cpf', 'rg', 'nationality', 'partner_name', 'partner_phone_number', 'partner_cpf', 'partner_rg', 'issuing_body', 'partner_issuing_body', 'pis'], 'string', 'max' => 255],

            // Campos usados para cadastro de consumer
            [
                ['rg', 'nationality', 'occupation_id', 'born_on', 'marital_status'],
                'required',
                'on' => 'consumer'
            ],
            [
                ['partner_name', 'partner_born_on', 'partner_phone_number', 'partner_rg'],
                'required',
                'on' => 'consumer',
                'when' => function($model) {
                    return $model->hasPartner();
                },
                'whenClient' => 'function(attribute, value) {
                    var stateCivil = $(\'select[name="PhysicalPerson[marital_status]"]\').val();
                    return stateCivil == "' . Yii::t('app', 'Married') . '" || stateCivil == "' . Yii::t('app', 'Cohabiting') . '";
                }'
            ],
            [['partner_cpf'], CpfValidator::className(), 'skipOnEmpty' => true],
            [['partner_born_on'], 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true],
            [['born_on'], 'date', 'format' => 'php:Y-m-d', 'max' => strtotime('today - 18 years'), 'skipOnEmpty' => true, 'tooBig' => Yii::t('app', 'The consumer must be eighteen.')],
            [['marital_status'], 'in', 'range' => self::getMaritalStatusList(), 'skipOnEmpty' => true],
            [['pis'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'name' => Yii::t('app', 'Name'),
            'cpf' => Yii::t('app', 'CPF'),
            'pis' => Yii::t('app' , 'PIS'),

            // Consumer CRUD
            'rg' => Yii::t('app', 'RG'),
            'nationality' => Yii::t('app', 'Nationality'),
            'occupation_id' => Yii::t('app', 'Occupation'),
            'born_on' => Yii::t('app', 'Born on'),
            'marital_status' => Yii::t('app', 'Marital status'),

            // Marital status = Married
            'partner_name' => Yii::t('app', 'Partner name'),
            'partner_born_on' => Yii::t('app', 'Partner born on'),
            'partner_phone_number' => Yii::t('app', 'Partner phone number'),
            'partner_cpf' => Yii::t('app', 'Partner CPF'),
            'partner_rg' => Yii::t('app', 'Partner RG'),
            'issuing_body' => Yii::t('app', 'Issuing Body'),
            'partner_issuing_body' => Yii::t('app', 'Issuing Body'),
        ];
    }

    /**
     * @return string[]
     */
    public static function getMaritalStatusList()
    {
        $statesCivis = array_merge(self::getMarriedMaritalStatusList(), self::getSingleMaritalStatusList());
        sort($statesCivis);
        return $statesCivis;
    }

    /**
     * @return string[]
     */
    public static function getMarriedMaritalStatusList()
    {
        return [
            Yii::t('app', 'Married'),
            Yii::t('app', 'Cohabiting'),
        ];
    }

    /**
     * @return string[]
     */
    public static function getSingleMaritalStatusList()
    {
        return [
            Yii::t('app', 'Single'),
            Yii::t('app', 'Divorced'),
            Yii::t('app', 'Widowed'),
            Yii::t('app', 'Separated'),
        ];
    }

    /**
     * @return boolean true
     */
    public function hasPartner()
    {
        return in_array($this->marital_status, self::getMarriedMaritalStatusList());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOccupation()
    {
        return $this->hasOne(Occupation::className(), ['id' => 'occupation_id']);
    }
}
