<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;

use Yii;

/**
 * This is the model class for table "transaction_details".
 *
 * @property integer $id
 * @property string $object_type
 * @property integer $object_id
 * @property integer $consumer_id
 * @property integer $plane_id
 * @property double $profit_percentage
 * @property double $profit
 * @property integer $transaction_origin
 *
 * @property Consumers $consumer
 * @property Planes $plane
 */
class TransactionDetail extends \yii\db\ActiveRecord
{
    const TRANSACTION_ORIGIN_HIM = 1;
    const TRANSACTION_ORIGIN_NET = 2;
    const REPRESENTATIVE_COMISSION = 3;
    const SALE_COMISSION = 4;

    public static function tableName()
    {
        return 'transaction_details';
    }

    public function rules()
    {
        return [
            [['object_type', 'object_id', 'consumer_id', 'plane_id', 'profit_percentage', 'profit', 'transaction_origin'], 'required'],
            [['object_id', 'consumer_id', 'plane_id', 'transaction_origin'], 'integer'],
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['profit_percentage', 'profit'], 'number'],
            [['object_type'], 'string', 'max' => 255],
            [['object_type'], 'in', 'range' => ['Sale', 'Consumer', 'SaleComission']],
            [['transaction_origin'], 'in', 'range' => [self::TRANSACTION_ORIGIN_HIM, self::TRANSACTION_ORIGIN_NET, self::REPRESENTATIVE_COMISSION, self::SALE_COMISSION]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'object_type' => Yii::t('app', 'Object Type'),
            'object_id' => Yii::t('app', 'Object ID'),
            'consumer_id' => Yii::t('app', 'Consumer ID'),
            'plane_id' => Yii::t('app', 'Plane ID'),
            'profit_percentage' => Yii::t('app', 'Profit Percentage'),
            'profit' => Yii::t('app', 'Profit'),
            'transaction_origin' => Yii::t('app', 'Transaction Origin'),
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
        ];
    }

    public function getConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'consumer_id']);
    }

    public function getPlane()
    {
         return $this->hasOne(Plane::className(), ['id' => 'plane_id']);
    }

    public function getObject()
    {
        if ($this->object_type == 'Sale') {
            return $this->hasOne(Sale::className(), ['id' => 'object_id']);
        } elseif ($this->object_type == 'Consumer') {
            return $this->hasOne(Consumer::className(), ['id' => 'object_id']);
        } elseif ($this->object_type == 'SaleComission'){
            return $this->hasOne(Sale::className(), ['id' => 'object_id']);
        }
    }
}
