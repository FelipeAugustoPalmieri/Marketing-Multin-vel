<?php
namespace app\models;

use Yii;
use app\models\Sale;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SalesReport extends Model
{
    public $inicio_periodo;
    public $fim_periodo;
    public $convenio;
    public $businessObject;

    public function rules()
    {
        return [
            [['inicio_periodo', 'fim_periodo'], 'required'],
            [['inicio_periodo', 'fim_periodo', 'convenio'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'inicio_periodo' => Yii::t('app', 'Begin Date'),
            'fim_periodo' => Yii::t('app', 'End Date'),
            'convenio' =>  Yii::t('app', 'Business'),
        ];
    }

    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
    }

    public function getSalesReport()
    {
        $query = Sale::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (\Yii::$app->user->getIdentity()->authenticable_type == 'Business') {

            $query->andWhere(
                'business_id = :businessId',
                [':businessId' => \Yii::$app->user->getIdentity()->authenticable_id]
            );
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $dataInicio = new \DateTime($this->inicio_periodo . ' 00:00:01');
        $dataFim = new \DateTime($this->fim_periodo . ' 23:59:59');

        $query->andFilterWhere(['between', 'sold_at', $dataInicio->format('Y-m-d H:i:s'), $dataFim->format('Y-m-d H:i:s')]);

        if ($this->convenio) {
            $query->andWhere(
                'business_id = :businessId',
                [':businessId' => $this->convenio]
            );
        }

        return $dataProvider;
    }

    public static function getTotal($dataA = null, $dataB = null, $convenio = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $convenio)->sum('total');

        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalFees($dataA = null, $dataB = null, $convenio = null)
    {
        $result = self::getModelTotalizador($dataA, $dataB, $convenio)->sum('fees + fees_adm');

        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    public static function getTotalRows($dataA = null, $dataB = null, $convenio = null){
        $result = self::getModelTotalizador($dataA, $dataB, $convenio)->count('id');

        if ($result == null) {
            return '0';
        } else{
            return $result;
        }
    }

    private static function getModelTotalizador($inicio = null, $fim = null, $convenio = null)
    {
        $model = Sale::find();

        if ($inicio && $fim) {

            $objIncio = new \DateTime($inicio . ' 00:00:01');
            $objFim = new \DateTime($fim . ' 23:59:59');

            $model->andFilterWhere(['between', 'sold_at', $objIncio->format('Y-m-d H:i:s'), $objFim->format('Y-m-d H:i:s')]);
        }

        if ($convenio) {
            $model->andWhere(
                'business_id = :businessId',
                [':businessId' => $convenio]
            );
        }

        return $model;
    }
}
