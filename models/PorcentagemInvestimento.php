<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Codeception\Util\Debug;

/**
 * This is the model class for table "investimento_porcentagem".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property double $porcentagem
 * @property string $data_referencia
 * @property PlaneInvestimento $planeinvestimento
 *
 */

class PorcentagemInvestimento extends \yii\db\ActiveRecord
{
    protected $unsharedRegisteredFees;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'investimento_porcentagem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_referencia'], 'date', 'format' => 'php:Y-m-d'],
            [['porcentagem'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'porcentagem' => Yii::t('app', 'Porcentagem'),
            'data_referencia' => Yii::t('app', 'Data Referencia'),
            'plane_investiment_id' => Yii::t('app', 'Plano Investimento')
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
                    return date('Y-m-d H:i:s');
                },
            ]
        ];
    }

    public function getPlaneInvestimento()
    {
        return $this->hasOne(PlanoInvestimento::className(), ['id' => 'plane_investiment_id']);
    }

}