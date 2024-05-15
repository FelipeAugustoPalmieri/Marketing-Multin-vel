<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yiibr\brvalidator\CnpjValidator;

/**
 * This is the model class for table "juridical_persons".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $company_name
 * @property string $trading_name
 * @property string $contact_name
 * @property string $cnpj
 * @property string $ie
 */
class JuridicalPerson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'juridical_persons';
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
            [['company_name', 'trading_name', 'contact_name', 'cnpj'], 'required'],
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['company_name', 'trading_name', 'contact_name', 'ie', 'cnpj'], 'string', 'max' => 255],
            [['cnpj'], CnpjValidator::className()],
            [['cnpj'], 'unique'],
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
            'company_name' => Yii::t('app', 'Company name'),
            'trading_name' => Yii::t('app', 'Trading name'),
            'contact_name' => Yii::t('app', 'Contact name'),
            'cnpj' => Yii::t('app', 'CNPJ'),
            'ie' => Yii::t('app', 'I.E.'),
        ];
    }
}
