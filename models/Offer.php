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
 * @property string $dt_inicial
 * @property string $dt_final
 * @property string $titulo
 * @property string $descricao
 * @property string $image
 * @property integer $convenio_id
 * @property integer $usuario_id
 *
 * @property Convenio $convenio
 */
class Offer extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'offer';
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
            ],
        ];
    }

    public function rules()
    {
        return [
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['convenio_id', 'usuario_id'], 'integer'],
            [['titulo', 'descricao', 'convenio_id', 'usuario_id'], 'required'],
            [['titulo'], 'string', 'max' => 100],
            [['image'], 'string', 'max' => 255],
        ];
    }

    public function scenarios()
    {
        $safeAttributes = [
            'titulo', 'descricao', 'dt_inicial', 'dt_final', 'image', 'convenio_id',
        ];
        return [
            'default' => array_merge($safeAttributes, []),
            'insert' => array_merge($safeAttributes, []),
            'update' => array_merge($safeAttributes, []),
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'dt_inicial' => Yii::t('app', 'Data Inicial'),
            'dt_final' => Yii::t('app', 'Data Final'),
            'titulo' => Yii::t('app', 'Titulo'),
            'descricao' => Yii::t('app', 'Descrição'),
            'convenio_id' => Yii::t('app', 'Convênio'),
            'usuario_id' => Yii::t('app', 'Usuario'),
            'image' => Yii::t('app', 'Imagem'),

            'nomeconvenio' => Yii::t('app', 'Business'),
        ];
    }

    public function getBusiness()
    {
        return $this->hasOne(Business::className(), ['id' => 'convenio_id']);
    }
}