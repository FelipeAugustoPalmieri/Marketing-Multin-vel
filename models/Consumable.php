<?php

namespace app\models;

use app\models\query\ConsumableQuery;
use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "consumables".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $business_id
 * @property string $description
 * @property double $shared_percentage
 * @property double $shared_percentage_adm
 *
 * @property Businesses $business
 */
class Consumable extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'consumables';
    }

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
    public static function find()
    {
        return new ConsumableQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['business_id', 'description', 'shared_percentage'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['business_id'], 'integer'],
            [['shared_percentage','shared_percentage_adm'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
            [['description'], 'string', 'max' => 255],
            ['shared_percentage', 'compare', 'compareValue' => 0.01, 'operator' => '>'],
            ['shared_percentage', 'compare', 'compareValue' => 0.01, 'operator' => '>='],
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
            'business_id' => Yii::t('app', 'Business ID'),
            'description' => Yii::t('app', 'Description'),
            'shared_percentage' => Yii::t('app', 'Shared Percentage'),
            'shared_percentage_adm' => Yii::t('app', 'Shared Percentage Administrator'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBusiness()
    {
        return $this->hasOne(Business::className(), ['id' => 'business_id']);
    }
}
