<?php

namespace app\models;

use Yii;

class Configuration extends \yii\db\ActiveRecord
{
    const PERCENTUAL_REPASSE_CONSUMIDOR = 1;
    const PERCENTUAL_REPASSE_REPRESENTANTE = 2;
    const PERCENTUAL_SALE_COMISSION = 7;
    const ID_CONVENIO_BUSINESS = 3;
    const ID_CONVENIO_INVESTIMENTO = 4;
    const ID_QUANTIDADE_MAXIMO_PARCELAS = 5;
    const ID_PERCENTUAL_PARCELAS = 6;
    const CONFIGURACAO_PARCELA_TITULO_INVESTIMENTO = 8;
    const VALOR_INVESTIMENTO = 9;

    public static function tableName()
    {
        return 'configurations';
    }

    public function rules()
    {
        return [
            [['name', 'type', 'value'], 'required'],
            [['name', 'type'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['type'], 'in', 'range' => array_keys(self::getTypes())],
            [['value'], 'validateValueType'],
        ];
    }

    public function validateValueType($attribute, $params = [])
    {
        if ($this->type == 'float' || $this->type == 'integer') {
            $label = $this->getAttributeLabel($attribute);

            if (!is_numeric($this->$attribute)) {
                return $this->addError($attribute, Yii::t('yii', '{attribute} must be a number.', ['attribute' => $label]));
            }

            if ($this->type == 'integer' && round($this->$attribute) != floatval($this->$attribute)) {
                return $this->addError($attribute, Yii::t('yii', '{attribute} must be an integer.', ['attribute' => $label]));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @return string[] where the key is the type code and the value its name
     */
    public static function getTypes()
    {
        return [
            'float' => Yii::t('app', 'Float'),
            'integer' => Yii::t('app', 'Integer'),
            'string' => Yii::t('app', 'String'),
        ];
    }

    /**
     * @return string
     */
    public function getTypeDescription()
    {
        if (isset(self::getTypes()[$this->type])) {
            return self::getTypes()[$this->type];
        }
    }

    /**
     * @return mixed
     */
    public static function getConfigurationValue($id)
    {
        $configuracao = self::findOne($id);
        if (!$configuracao) {
            return null;
        }

        if ($configuracao->type == 'float') {
            return (float) $configuracao->value;
        }

        if ($configuracao->type == 'integer') {
            return (int) $configuracao->value;
        }

        return $configuracao->value;
    }
}
