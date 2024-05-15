<?php

namespace app\models\financeiro;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;

class ContasReceberParcelas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'financeiro_contas_receber_parcelas';
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
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['receber_id', 'nr_parcela', 'valor', 'status'], 'required'],
            [['receber_id'], 'integer']
        ];
    }

    public function scenarios()
    {
        $safeAttributes = [
            'receber_id', 'nr_parcelas', 'valor', 'vencimento',
        ];
        return [
            'default' => $safeAttributes,
            'insert' => $safeAttributes,
            'update' => $safeAttributes,
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
            'object_id' => Yii::t('app', 'Type ID'),
            'nr_parcelas' => Yii::t('app', 'Nr Parcelas'),
            'valor' => Yii::t('app', 'Value'),
        ];
    }

    public function prepareSave($contasreceber, $parcelas){
        $this->receber_id = $contasreceber->id;
        $this->nr_parcela = $parcelas;
        $this->valor = $contasreceber->valor / $contasreceber->nr_parcelas;
        $this->status = 1;
    }
}