<?php
namespace app\models;

use Yii;
use app\models\TransactionDetail;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TransactionReport extends Model
{
    public $period;
    public $user;
    public $consumer_id;
    public $inicio_periodo;
    public $fim_periodo;
    public $minimovalor;
    public $xInfContaTotal;
    public $xListaVendas;
    public $xInfConsumidor;
    public $xOrderPlanos;
    public $xOrderNome;
    public $xImpressDeposit;
    public $xShowCabecalho;


    public function rules()
    {
        return [
            [['period', 'user'], 'required'],
            [['period', 'user', 'consumer_id'], 'safe'],
            [['minimovalor'],'number']
        ];
    }

    public function attributeLabels()
    {
        return [
            'period' => Yii::t('app', 'Month'),
            'inicio_periodo' => Yii::t('app', 'Begin Date'),
            'fim_periodo' => Yii::t('app', 'End Date'),
            'minimovalor' => Yii::t('app', 'Min Value'),
            'user' => Yii::t('app', 'User'),
            'consumer_id' => Yii::t('app', 'Consumer'),
        ];
    }

    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
    }

    public function getTransactionReport()
    {
        $query = TransactionDetail::find();

        $query->andWhere(
            ['transaction_origin' => [
                    TransactionDetail::TRANSACTION_ORIGIN_HIM,
                    TransactionDetail::TRANSACTION_ORIGIN_NET,
                    TransactionDetail::SALE_COMISSION
            ]]
        );

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($this->user->authenticable_type == 'Consumer') {
            $query->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => $this->user->consumer->id]
            );
        } else if ($this->consumer_id != null) {
            $query->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => $this->consumer_id]
            );
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        list($ano, $mes) = explode("-", $this->period);
        $lastDay = date("t", mktime(0,0,0,$mes,'01',$ano));

        $startDate = new \DateTime($ano.'-'.$mes.'-01 00:00:01');
        $endDate = new \DateTime($ano.'-'.$mes.'-'.$lastDay.' 23:59:59');


        $query->andFilterWhere([
            'between',
            'created_at',
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        ]);

        return $dataProvider;
    }

    public function getTransactionReportExterno($consumer_id, $dataA, $dataB)
    {
        $query = TransactionDetail::find();

        $query->andWhere(
            ['transaction_origin' => [
                    TransactionDetail::TRANSACTION_ORIGIN_HIM,
                    TransactionDetail::TRANSACTION_ORIGIN_NET,
                    TransactionDetail::SALE_COMISSION
            ]]
        );

        if ($consumer_id != null) {
            $query->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => $consumer_id]
            );
        }

        $query->andFilterWhere([
            'between',
            'created_at',
            $dataA->format('Y-m-d H:i:s'),
            $dataB->format('Y-m-t 23:59:59')
        ]);

        return $query->all();
    }

    public static function getTotal($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalPeriodo($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizadorPeriodo($dataA, $dataB, $consumer)->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalSaleNet($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Sale', 'transaction_origin' => TransactionDetail::TRANSACTION_ORIGIN_NET])->sum('profit');

        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalSaleNetPeriodo($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizadorPeriodo($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Sale', 'transaction_origin' => TransactionDetail::TRANSACTION_ORIGIN_NET])->sum('profit');

        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalSaleHim($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Sale', 'transaction_origin' => TransactionDetail::TRANSACTION_ORIGIN_HIM])->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalSaleHimPeriodo($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizadorPeriodo($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Sale', 'transaction_origin' => TransactionDetail::TRANSACTION_ORIGIN_HIM])->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalActivation($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Consumer'])->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalActivationPeriodo($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizadorPeriodo($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Consumer'])->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalSale($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->andwhere(['object_type' => 'Sale'])->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    private static function getModelTotalizadorPeriodo($dateA, $dateB, $consumer = null)
    {
        $model = TransactionDetail::find();

        $model->andWhere(
            ['transaction_origin' => [
                    TransactionDetail::TRANSACTION_ORIGIN_HIM,
                    TransactionDetail::TRANSACTION_ORIGIN_NET
            ]]
        );

        if (\Yii::$app->user->getIdentity()->authenticable_type == 'Consumer') {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => \Yii::$app->user->getIdentity()->authenticable_id]
            );
        }

        if ($dateA and $dateB) {

            $model->andFilterWhere([
                'between',
                'created_at',
                $dateA->format('Y-m-d H:i:s'),
                $dateB->format('Y-m-t 23:59:59')
            ]);
        }

        if ($consumer) {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => $consumer]
            );
        }

        return $model;
    }

    private static function getModelTotalizador($period, $consumer = null)
    {
        $model = TransactionDetail::find();

        $model->andWhere(
            ['transaction_origin' => [
                    TransactionDetail::TRANSACTION_ORIGIN_HIM,
                    TransactionDetail::TRANSACTION_ORIGIN_NET
            ]]
        );

        if (\Yii::$app->user->getIdentity()->authenticable_type == 'Consumer') {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => \Yii::$app->user->getIdentity()->authenticable_id]
            );
        }

        if ($period) {

            list($ano, $mes) = explode("-", $period);
            $lastDay = date("t", mktime(0,0,0,$mes,'01',$ano));

            $startDate = new \DateTime($ano.'-'.$mes.'-01 00:00:01');
            $endDate = new \DateTime($ano.'-'.$mes.'-'.$lastDay.' 23:59:59');

            $model->andFilterWhere([
                'between',
                'created_at',
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            ]);
        }

        if ($consumer) {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => $consumer]
            );
        }

        return $model;
    }
}
