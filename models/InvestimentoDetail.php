<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;


use Yii;

/**
 * This is the model class for table "transaction_details".
 *
 * @property integer $id
 * @property string $investiment_at
 * @property integer $invoice_code
 * @property integer $consumer_id
 * @property integer $sold_id
 * @property double $total
 * @property double $interest
 * @property double $balance
 * @property integer $processed
 *
 * @property Consumers $consumer
 * @property Sales $sale
 */
class InvestimentoDetail extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'investimento_details';
    }

    public function rules()
    {
        return [
            [['total', 'consumer_id', 'invoice_code'], 'required'],
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['total'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'investiment_at' => Yii::t('app', 'Investiment Date'),
            'total' => Yii::t('app', 'Total'),
            'invoice_code' => Yii::t('app', 'Invoice Code'),
            'consumer_id' => Yii::t('app', 'Consumer ID'),
            'interest' => Yii::t('app', 'Interest'),
            'balance' => Yii::t('app', 'Balance'),
            'processed' => Yii::t('app', 'processed'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    if($this->created_at){
                        $datetime = new \DateTime($this->created_at);
                        return date("Y-m-d H:i:s", $datetime->getTimestamp());
                    }else{
                        return date('Y-m-d H:i:s');
                    }
                    
                },
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'investiment_at',
                'updatedAtAttribute' => NULL,
                'value' => function () {
                    if($this->investiment_at){
                        $datetime = new \DateTime($this->investiment_at);
                        return date("Y-m-d H:i:s", $datetime->getTimestamp());
                    }else{
                        return date('Y-m-d H:i:s');
                    }
                    
                },
            ]
        ];
    }

    public function getConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'consumer_id']);
    }

    public function getSale()
    {
        return $this->hasOne(Sales::className(), ['id' => 'sold_id']);
    }
}
