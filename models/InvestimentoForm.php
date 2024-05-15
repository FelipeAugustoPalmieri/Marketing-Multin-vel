<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use yii\base\Model;
use Yii;

/**
 * This is the model class for table "transaction_details".
 *
 * @property integer $id
 * @property string $investiment_at
 *
 */
class InvestimentoForm extends Model
{
    public $data_contrato;
    public $valor_contrato;

    public function rules()
    {
        return [
            [['data_contrato', 'valor_contrato'], 'required'],
        ];
    }

    public function scenarios()
    {
        return [
            'insert' => ['data_contrato', 'valor_contrato'],
            'update' => ['data_contrato', 'valor_contrato'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'data_contrato' => Yii::t('app', 'Date in Investiment'),
            'valor_contrato' => Yii::t('app', 'Value'),
        ];
    }
}