<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

use Yii;

class PlanoInvestimento extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'plano_investimento';
    }

    public function rules()
    {
        return [
            [['nome', 'quantidade_meses'], 'required'],
            [['nome'], 'string', 'max' => 100]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Name Plane'),
            'quantidade_meses' => Yii::t('app', 'Quantidade Meses'),
        ];
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
}