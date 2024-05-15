<?php

namespace app\models;

use Yii;

class SalesRepresentativeCity extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sales_representative_cities';
    }
    public function rules()
    {
        return [
            [['sales_representative_id', 'city_id'], 'required'],
            [['sales_representative_id', 'city_id'], 'integer'],
            [['city_id'], 'unique', 'message' => Yii::t('app/error', 'This city is alredy in use.')],
            ['city_id', 'unique', 'targetAttribute' => ['city_id', 'sales_representative_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sales_representative_id' => Yii::t('app', 'Sales Representative ID'),
            'city_id' => Yii::t('app', 'City ID'),
        ];
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getSalesRepresentative()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'sales_representative_id']);
    }
}
