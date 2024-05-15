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
 * @property double $valor
 * @property integer $object_id
 * @property string $payment_method
 * @property string $payment_id
 * @property string $url_invoice
 * @property string $payment_customer_id
 *
 */
class Faturamento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'faturamento';
    }

    public $parcela;

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
            [['object_id'], 'integer'],
            [['person_class'], 'in', 'range' => ['PhysicalPerson', 'JuridicalPerson']],
            [['person_class', 'object_id', 'payment_method', 'valor', 'payment_id', 'url_invoice', 'payment_customer_id'], 'required'],
            [['url_invoice'], 'url'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $safeAttributes = [
            'valor', 'payment_method', 'payment_id', 'url_invoice', 'payment_customer_id',
        ];
        return [
            'default' => array_merge($safeAttributes, ['person_class', 'object_id']),
            'insert' => array_merge($safeAttributes, ['person_class', 'object_id']),
            'update' => array_merge($safeAttributes, ['person_class', 'object_id']),
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
            'object_id' => Yii::t('app', 'Object Id'),
            'valor' => Yii::t('app', 'Value'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'payment_id' => Yii::t('app', 'Payment Id'),
            'url_invoice' => Yii::t('app', 'Url Invoice'),
            'payment_customer_id' => Yii::t('app', 'Payment Customer Id'),
            'parcela' => Yii::t('app', 'Payment Method'),
        ];
    }

    public function prepareSave($consumer, $valor, $dadosRetorno){
        $this->person_class = $consumer->legalPerson->person_class;
        $this->valor = $valor;
        $this->object_id = $consumer->id;
        $this->payment_method = "Pagamento Asaas";
        $this->payment_id = $dadosRetorno->id;
        $this->url_invoice = $dadosRetorno->invoiceUrl;
        $this->payment_customer_id = $consumer->id_asaas;
    }

    public function getPerson()
    {
        if ($this->person_class == 'JuridicalPerson') {
            return $this->hasOne(JuridicalPerson::className(), ['id' => 'object_id']);
        } elseif ($this->person_class == 'PhysicalPerson') {
            return $this->hasOne(PhysicalPerson::className(), ['id' => 'object_id']);
        }
    }
}