<?php
namespace app\models;

use Yii;
use app\models\InvestimentoDetail;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvestimentoDetailReport extends Model
{
    public $user;
    public $consumer_id;
    public $inicio_periodo;
    public $fim_periodo;


    public function rules()
    {
        return [
            [['user'], 'required'],
            [['user', 'consumer_id'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'inicio_periodo' => Yii::t('app', 'Begin Date'),
            'fim_periodo' => Yii::t('app', 'End Date'),
            'consumer_id' => Yii::t('app', 'Consumer'),
        ];
    }

    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
    }

    public function getTransactionReport()
    {
        $query = InvestimentoDetail::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($this->user->authenticable_type == 'Consumer') {
            $query->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => intval($this->user->consumer->id)]
            );
        } else if ($this->consumer_id != null) {
            $query->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => intval($this->consumer_id)]
            );
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->inicio_periodo and $this->fim_periodo) {
            $query->andFilterWhere([
                'between',
                'investiment_at',
                $this->inicio_periodo,
                $this->fim_periodo,
            ]);
        }

        $query->orderBy(['investiment_at'=>SORT_ASC]);

        return $dataProvider;
    }

    public static function getSaldoLinha($idTransacao = null)
    {
        $result = self::getModelTotalizadorSaldo($idTransacao)->sum('ROUND(CAST(investimento_details.total as numeric), 2)');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotal($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->sum('ROUND(CAST(total as numeric), 2)');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalRows($dataA = null, $dataB = null, $consumer = null){
        $result = self::getModelTotalizador($dataA, $dataB, $consumer)->count('id');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalJuros($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizadorJuros($dataA, $dataB, $consumer)->sum('ROUND(CAST(total as numeric), 2)');
        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalInvestimento($dataA = null, $dataB = null, $consumer = null)
    {
        $result = self::getModelTotalizadorInvestimento($dataA, $dataB, intval($consumer))->sum('total');

        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    private static function getModelTotalizadorInvestimento($dateA, $dateB, $consumer = null)
    {
        $model = InvestimentoDetail::find();

        $model->andWhere(
            ['interest' => 0]
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
                'investiment_at',
                $dateA,
                $dateB
            ]);
        }

        if ($consumer) {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => intval($consumer)]
            );
        }

        return $model;
    }

    private static function getModelTotalizadorJuros($dateA, $dateB, $consumer = null)
    {
        $model = InvestimentoDetail::find();

        $model->andWhere(
            ['interest' => 1]
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
                'investiment_at',
                $dateA,
                $dateB
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

    private static function getModelTotalizadorSaldo($idTransacao)
    {
        $investimento = InvestimentoDetail::find()->where(['id'=>$idTransacao])->one();

        $model = InvestimentoDetail::find()
                //->leftJoin('investimento_details inv2', 'inv2.sold_id = investimento_details.sold_id AND inv2.interest = 1')
                ->where(['<=', 'investimento_details.investiment_at', $investimento->investiment_at])
                //->andWhere(['<=','investimento_details.id',$investimento->id])
                ->andWhere(['=','investimento_details.consumer_id', $investimento->consumer_id]);
        return $model;
    }

    private static function getModelTotalizador($dateA, $dateB, $consumer = null)
    {
        $model = InvestimentoDetail::find();

        if (\Yii::$app->user->getIdentity()->authenticable_type == 'Consumer') {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => \Yii::$app->user->getIdentity()->authenticable_id]
            );
        }

        if ($dateA and $dateB) {
            $model->andFilterWhere([
                'between',
                'investiment_at',
                $dateA,
                $dateB
            ]);
        }

        if ($consumer) {
            $model->andWhere(
                'consumer_id = :consumerId',
                [':consumerId' => intval($consumer)]
            );
        }

        $model->orderBy(['balance'=>SORT_DESC]);

        return $model;
    }
}
