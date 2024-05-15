<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "qualifications".
 *
 * @property integer $id
 */
class Qualification extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'qualifications';
    }

    public function rules()
    {
        return [
            [['description', 'position'], 'required'],
            [['gain_percentage'], 'number','min' => 0, 'max' => 100 ],
            [['position'], 'integer', 'min' => 1],
            [['register_network_sale'], 'boolean'],
            [['completed_levels'], 'integer', 'min' => 1],
            [['description'], 'string', 'max' => 255],
            [['position', 'description', 'completed_levels'], 'unique'],
            [['points'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Description'),
            'gain_percentage' => Yii::t('app', 'Gain Percentage'),
            'position' => Yii::t('app', 'Position'),
            'completed_levels' => Yii::t('app', 'Completed Levels'),
            'register_network_sale' => Yii::t('app', 'Register Network Sale'),
            'points' => Yii::t('app', 'Points'),
        ];
    }

    public function getRequiredConsumers()
    {
        //calculo da quantidade de nós em um nível em uma árvore binária
        $nodeQuant = pow(2, $this->completed_levels) - 1;

        //remove o primeiro (ele mesmo)
        return $nodeQuant - 1;
    }
}
