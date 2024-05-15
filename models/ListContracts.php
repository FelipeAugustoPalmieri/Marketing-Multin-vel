<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

use Yii;

class ListContracts extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'list_contracts';
    }

    public function rules()
    {
        return [
            [['usuario_id', 'object_id', 'contract'], 'required'],
            [['usuario_id', 'object_id', 'usuario_id_cancelo'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'contract' => Yii::t('app', 'contract'),
            'created_at' => Yii::t('app', 'created_at'),
            'updated_at' => Yii::t('app', 'updated_at'),
        ];
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
    public function CancelContract(){
        $transaction = $this->getDb()->beginTransaction();
        if ($this->updateAttributes(['is_cancel' => 1, 'usuario_id_cancelo'=>Yii::$app->user->id])) {
            $transaction->commit();
            return true;
        }
    }

    public function getContractParent()
    {
        return $this->hasOne(Contratos::className(), ['id' => 'contract_id']);
    }
}