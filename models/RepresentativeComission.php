<?php
namespace app\models;

use Yii;
use app\models\Consumer;
use app\models\TransactionDetail;
use app\helpers\TransactionReportHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RepresentativeComission extends Model
{
    public $period;
    public $user;
    public $consumer_representative_id;

    public $month;
    public $year;

    public function rules()
    {
        return [
            [['user', 'period'], 'required'],
            [['period', 'user', 'consumer_representative_id', 'month', 'year'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'period' => Yii::t('app', 'Month'),
            'user' => Yii::t('app', 'User'),
            'consumer_representative_id' => Yii::t('app', 'Representative'),
        ];
    }

    public function getTransactionReport()
    {
        $query = TransactionDetail::find();


        $query->andWhere(
            ['transaction_origin' => [
                    TransactionDetail::REPRESENTATIVE_COMISSION,
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
        } else if ($this->consumer_representative_id != null) {
            $query->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => $this->consumer_representative_id]
            );
        }
        
        if (!$this->validate()) {
            return $dataProvider;
        }

        list($ano, $mes) = explode("-", $this->period);
        $lastDay = date("t", mktime(0,0,0,$mes,'01',$ano));

        $startDate = new \DateTime($ano.'-'.$mes.'-01 00:00:01');
        $endDate = new \DateTime($ano.'-'.$mes.'-'.$lastDay.' 23:59:59');


        $query->andWhere([
            'between',
            'created_at',
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        ]);


        return $dataProvider;
    }

    public static function getTotalComission($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->sum('profit');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalValue($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->sum();
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    private static function getModelTotalizador($period, $consumer = null)
    {
        $model = TransactionDetail::find();

        $model->andWhere(
            ['transaction_origin' => [
                    TransactionDetail::REPRESENTATIVE_COMISSION,
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