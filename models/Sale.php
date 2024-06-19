<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Codeception\Util\Debug;

/**
 * This is the model class for table "sales".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $sold_at
 * @property double $total
 * @property string $invoice_code
 * @property integer $consumer_id
 * @property integer $consumer_sale_id
 * @property integer $business_id
 * @property integer $consumable_id
 * @property double $points
 * @property double $fees
 * @property double $shared_percentage
 * @property double $plane_multiplier
 * @property integer $plane_id
 * @property double $unshared_fees
 * @property double $fees_adm
 *
 * @property Businesses $business
 * @property Consumables $consumable
 * @property Consumers $consumer
 */
class Sale extends \yii\db\ActiveRecord
{
    protected $unsharedRegisteredFees;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total', 'invoice_code', 'consumer_id', 'business_id', 'consumable_id'], 'required'],
            [['created_at', 'updated_at', 'sold_at'], 'safe'],
            [['total', 'points', 'fees', 'shared_percentage', 'plane_multiplier', 'unshared_fees'], 'number'],
            [['sold_at'], 'date'],
            [['consumer_id', 'business_id', 'consumable_id', 'plane_id'], 'integer'],
            [['invoice_code'], 'string'],
            ['invoice_code', 'unique', 'targetAttribute' => ['invoice_code', 'business_id'], 'message' => Yii::t('app/error', 'This invoice was posted on another sale')],
            ['total', 'compare', 'compareValue' => 0.01, 'operator' => '>'],
            ['total', 'compare', 'compareValue' => 0.01, 'operator' => '>='],
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
            'sold_at' => Yii::t('app', 'Sold At'),
            'total' => Yii::t('app', 'Total'),
            'invoice_code' => Yii::t('app', 'Invoice Code'),
            'consumer_id' => Yii::t('app', 'Consumer ID'),
            'consumer_sale_id' => Yii::t('app','Consumer Sale Id'),
            'business_id' => Yii::t('app', 'Business ID'),
            'consumable_id' => Yii::t('app', 'Consumable ID'),
            'points' => Yii::t('app', 'Network Fees'),
            'fees' => Yii::t('app', 'Fees'),
            'shared_percentage' => Yii::t('app', 'Shared Percentage'),
            'plane_multiplier' => Yii::t('app', 'Multiplier'),
            'plane_id' => Yii::t('app', 'Plane ID'),
            'unshared_fees' => Yii::t('app', 'Unshared Fees'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'sold_at',
                'updatedAtAttribute' => false,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributes = null)
    {
        $transaction = $this->getDb()->beginTransaction();

        try {

            $this->points = $this->calculatePoints();
            $this->fees = $this->calculateFees();
            $this->shared_percentage = $this->consumable->shared_percentage;
            $this->plane_multiplier = $this->consumer->plane->multiplier;
            $this->plane_id = $this->consumer->plane->id;

            $result = parent::save($runValidation, $attributes);
            
            if ($result && $this->registerTransactionDetails()) {
                if ($this->updateAttributes(['unshared_fees' => $this->unsharedRegisteredFees])) {
                    $transaction->commit();
                    return true;
                }
            }

        } catch (\Exception $e) {
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
        }

        $transaction->rollback();
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBusiness()
    {
        return $this->hasOne(Business::className(), ['id' => 'business_id']);
    }

    public function UpdateDateSoad($date, $id){
        $connection = \Yii::$app->db;
        $connection->createCommand()
        ->update('public.sales', ['sold_at'=>$date], 'id = '.$id)
        ->execute();

        $modelTransaction = New TransactionDetail;
        $modelTransaction = $modelTransaction->findAll(['object_id'=>$id, 'object_type'=>'Sale']);
        foreach ($modelTransaction as $key => $value) {
            $value->created_at = $date;
            $value->save();
        }
        
    }

    public function UpdateTotal($runValidation = true, $attributes = null){
        $this->points = $this->calculatePoints();
        $this->fees = $this->calculateFees();
        $this->shared_percentage = $this->consumable->shared_percentage;
        
        $connection = \Yii::$app->db;
        $connection->createCommand()
        ->update('public.sales', ['total'=>$this->total, 'points'=> $this->points, 'fees'=> $this->fees, 'shared_percentage'=>$this->shared_percentage], 'id = '.$this->id)
        ->execute();   
        
        $modelTransaction = New TransactionDetail;
        $modelTransaction = $modelTransaction->findAll(['object_id'=>$this->id, 'object_type'=>'Sale']);

        $this->load('consumer');

        $representative = Consumer::getRepresentativeOfCity($this->getRepresentativeCity());

        foreach ($modelTransaction as $key => $value) {
            if($value->id == $representative->id){
                $value->profit = $this->calculateRepresentativeSaleProfit();
            }else{
                $value->profit = $this->calculateProfitValue($this->consumer, $value->profit_percentage);
            }
            $value->save();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumable()
    {
        return $this->hasOne(Consumable::className(), ['id' => 'consumable_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'consumer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumerSale()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'consumer_sale_id']);
    }

    /*
     * @return int
     */
    public function calculatePoints()
    {
        if (!$this->consumer->plane) {
            return 0;
        }

        $repasse = $this->calculateFees();
        return round($repasse * $this->consumer->plane->multiplier, 2);
    }

    /*
     * @return float
     */
    public function calculateFees()
    {
        if($this->consumable->shared_percentage_adm > 0){
            $valorTbest = round((($this->total * $this->consumable->shared_percentage) / 100), 2);
            $this->fees_adm = $valorTbest * ($this->consumable->shared_percentage_adm/100);
            return $valorTbest - $this->fees_adm;
        }else{
            return round((($this->total * $this->consumable->shared_percentage) / 100), 2);
        }
    }

    public function calculateRepresentativeSaleProfit()
    {
        return round(((Configuration::getConfigurationValue(Configuration::PERCENTUAL_REPASSE_REPRESENTANTE) * $this->calculateFees()) / 100), 2);
    }

    public function calculateSaleComissionProfit()
    {
        return round(((Configuration::getConfigurationValue(Configuration::PERCENTUAL_SALE_COMISSION) * $this->total) / 100), 2);
    }

    /*
     * @return boolean
     */
    protected function registerTransactionDetails()
    {
        $sharedFees = 0;

        $consumerQualification = $this->consumer->getCurrentQualification();
        if ($consumerQualification && $this->consumer->plane) {

            $percentualConsumidor = Configuration::getConfigurationValue(Configuration::PERCENTUAL_REPASSE_CONSUMIDOR);

            $details = new TransactionDetail;
            $details->object_type = 'Sale';
            $details->object_id = $this->id;
            $details->consumer_id = $this->consumer->id;
            $details->plane_id = $this->consumer->plane->id;
            $details->profit_percentage = $percentualConsumidor;
            $sharedFees += $details->profit = $this->calculateProfitValue($this->consumer, $percentualConsumidor);
            $details->transaction_origin = TransactionDetail::TRANSACTION_ORIGIN_HIM;

            if (!$details->save()) {
                return false;
            }
        }

        if($this->business_id == Configuration::getConfigurationValue(Configuration::ID_CONVENIO_INVESTIMENTO)){
            $investimento = new Investimento();
            $investimento = $investimento->find()->Where(['consumer_id' => $this->consumer->id])->one();
            if($investimento->primeira_parcela)
            {
                $detail_investimento = new InvestimentoDetail;
                $valor_adm = floor($this->fees_adm * 100) / 100;
                $detail_investimento->total = $this->total - ($this->fees + $valor_adm);
                $detail_investimento->invoice_code = $this->invoice_code;
                $detail_investimento->sold_id = $this->id;
                $detail_investimento->consumer_id = $this->consumer->id;

                $resultadoInvestimento = $detail_investimento->save();
            }else{
                $investimento->primeira_parcela = true;
                $investimento->save();
            }
        }

        $network = $this->consumer->getFatherTree();
        
        $consumerPatrocinador = Consumer::find()->Where(['id'=>303])->one();
        //echo '</br>consumer patrocinador: '.$consumerPatrocinador->id;
        $resultQualification = $consumerPatrocinador->UltimoConsumersNetWork();
        //$quantidadeQualification = (count($resultQualification)-1);
        //echo '</br></br>result Qualification: ';
        //echo $resultQualification[$quantidadeQualification];
        //echo '<br>Consumer: '.$this->consumer->id;
        //echo '<br>NetWork<br>';
        foreach ($network as $consumer) {
            echo '<br>consumer: '.$consumer->id.'<br>';
            
            if ($consumer->id == $this->consumer->id) {
                echo 'codigo Igual<br>';
                continue;
            }

            if (!$consumer->plane) {
                echo 'Não tem plano<br>';
                continue;
            }

            if (!$consumer->hasConsumerInQualificationNetwork($this->consumer)) {
                echo 'Consume Qualification<br>';
                continue;
            }

            $consumerQualification = $consumer->getCurrentQualification();
            if ($consumerQualification->register_network_sale !== true) {
                echo 'Não pode vender<br>';
                continue;
            }

            $details = new TransactionDetail;
            $details->object_type = 'Sale';
            $details->object_id = $this->id;
            $details->consumer_id = $consumer->id;
            $details->plane_id = $consumer->plane->id;
            $details->profit_percentage = $consumerQualification->gain_percentage;
            $sharedFees += $details->profit = $this->calculateProfitValue($consumer, $consumerQualification->gain_percentage);
            $details->transaction_origin = TransactionDetail::TRANSACTION_ORIGIN_NET;
            if (!$details->save()) {
                return false;
            }
        }
        //exit();

        if($this->consumer_sale_id > 0){
            $vendedor = $this->getConsumerSale()->one();

            $saleComission  = new TransactionDetail;
            $saleComission->object_type = 'SaleComission';
            $saleComission->object_id = $this->id;
            $saleComission->consumer_id = $vendedor->id;
            $saleComission->plane_id = $vendedor->plane_id;
            $saleComission->profit_percentage = Configuration::getConfigurationValue(Configuration::PERCENTUAL_SALE_COMISSION);

            $sharedFees += $saleComission->profit = $this->calculateSaleComissionProfit();

            $saleComission->transaction_origin = TransactionDetail::SALE_COMISSION;

            if(!$saleComission->save()){
                return false;
            }
        }

        $comissionSaved = true;
        $representative = Consumer::getRepresentativeOfCity($this->getRepresentativeCity());
        if ($representative) {
            $comission  = new TransactionDetail;
            $comission->object_type = 'Sale';
            $comission->object_id = $this->id;
            $comission->consumer_id = $representative->id;
            $comission->plane_id = $representative->plane_id;
            $comission->profit_percentage = Configuration::getConfigurationValue(Configuration::PERCENTUAL_REPASSE_REPRESENTANTE);
            $comission->profit = $this->calculateRepresentativeSaleProfit();
            $comission->transaction_origin = TransactionDetail::REPRESENTATIVE_COMISSION;

            $comissionSaved = $comission->save();
        }

        if (!$comissionSaved) {
            return false;
        }

        $this->unsharedRegisteredFees = $this->points - $sharedFees;

        return true;
    }

    public function getRepresentativeCity()
    {
        $codComissionRepresentative = array(Configuration::getConfigurationValue(Configuration::ID_CONVENIO_BUSINESS), Configuration::getConfigurationValue(Configuration::ID_CONVENIO_INVESTIMENTO));

        if (in_array($this->business->id, $codComissionRepresentative)) {
            $city = $this->consumer->legalPerson->city;
        } else {
            $city = $this->business->legalPerson->city;
        }

        return $city;
    }

    protected function calculateProfitValue(Consumer $consumer, $percentage)
    {
        $repasse = $this->calculateFees() * $consumer->plane->multiplier;
        return round((($repasse * $percentage) / 100), 2);
    }
    
}
