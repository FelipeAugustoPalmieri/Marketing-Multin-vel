<?php

namespace app\models\financeiro;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;

class ContasReceber extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'financeiro_contas_receber';
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

    public function scenarios()
    {
        $safeAttributes = [
            'faturamento_id', 'valor', 'nr_parcelas',
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
    public function rules()
    {
        return [
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['person_class'], 'in', 'range' => ['PhysicalPerson', 'JuridicalPerson']],
            [['person_class', 'faturamento_id', 'object_id', 'valor', 'nr_parcelas'], 'required'],
            [['object_id', 'faturamento_id'], 'integer']
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

    public function prepareSave($faturamento, $consumers, $nr_documento, $parcelas){
        $this->faturamento_id = $faturamento->id;
        $this->nr_documento = $nr_documento;
        $this->object_id = $consumers->id;
        $this->person_class = $consumers->legalPerson->person_class;
        $this->valor = $faturamento->valor;
        $this->nr_parcelas = $parcelas;
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