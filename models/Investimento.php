<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use kartik\mpdf\Pdf;
use app\models\ListContracts;
use app\models\Configuration;

use Yii;

/**
 * This is the model class for table "transaction_details".
 *
 * @property integer $id
 * @property string $investiment_at
 * @property integer $consumer_id
 * @property integer $dia_vencimento
 * @property double $prazo
 *
 * @property Consumers $consumer
 */
class Investimento extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'investimento';
    }

    public function rules()
    {
        return [
            [['prazo', 'valor', 'consumer_id', 'dia_vencimento'], 'required'],
            ['dia_vencimento', 'compare', 'compareValue' => 1, 'operator' => '>='],
            ['dia_vencimento', 'compare', 'compareValue' => 31, 'operator' => '<='],
            [['prazo'], 'in', 'range' => self::getPrazo()],
            [['!created_at', '!updated_at'], 'date', 'type' => 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'investiment_at' => Yii::t('app', 'Investiment Date'),
            'consumer_id' => Yii::t('app', 'Consumer ID'),
            'dia_vencimento' => Yii::t('app', 'Day Due'),
            'prazo' => Yii::t('app', 'Prazo'),
            'valor' => Yii::t('app', 'Valor'),
            'created_at' => Yii::t('app', 'Created At'),
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
                    if($this->created_at){
                        $datetime = new \DateTime($this->created_at);
                        return date("Y-m-d H:i:s", $datetime->getTimestamp());
                    }else{
                        return date('Y-m-d H:i:s');
                    }
                    
                },
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'investiment_at',
                'updatedAtAttribute' => NULL,
                'value' => function () {
                    if($this->investiment_at){
                        $datetime = new \DateTime($this->investiment_at);
                        return date("Y-m-d H:i:s", $datetime->getTimestamp());
                    }else{
                        return date('Y-m-d H:i:s');
                    }
                    
                },
            ]
        ];
    }

    public static function getPrazo()
    {
        $parcelasTituloInvestimento = Configuration::getConfigurationValue(Configuration::CONFIGURACAO_PARCELA_TITULO_INVESTIMENTO);
        $listaParcelas = explode(",", $parcelasTituloInvestimento);
        $retorno = [];
        foreach($listaParcelas as $key => $value){
            $retorno[] = (intval($value) * 12);
        }
        return $retorno;
    }

    public static function Valores()
    {
        return [
            Yii::$app->formatter->asCurrency(Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO)), 
            Yii::$app->formatter->asCurrency(300), 
            Yii::$app->formatter->asCurrency(500), 
            Yii::$app->formatter->asCurrency(1000)
        ];
    }

    public function getConsumer()
    {
        return $this->hasOne(Consumer::className(), ['id' => 'consumer_id']);
    }

    public function gravarInvestimento($consumer){
        $this->consumer_id = $consumer->id;
        $this->dia_vencimento = date("d");
        $this->prazo = $consumer->maximum_amount;
        $this->valor = Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO);
        $this->save();
    }

    public function GerarContratoInvestimento($destination = Pdf::DEST_DOWNLOAD, $investimentoForm = null, $x_visualizar = false){
        
        $listContratos = new ListContracts(); 
        $consumer = $this->consumer;
        $consumer->maximum_amount = $this->prazo;
        if($x_visualizar){
            $dados = false;
        }else{
            $dados = $listContratos->find()->Where(["object_type"=>"Investiment"])->andWhere(["object_id"=>$this->id])->andWhere(["is_cancel"=>0])->one();
        }

        if(!$dados){
            $contrato = new Contratos();

            $findContrato = $contrato->find()->Where(['flag_local'=>1])->andWhere(['flag_cancel'=>0])->orderBy(['created_at'=>SORT_DESC])->one();

            $dataContract = $consumer->getDataContract($this->created_at, $investimentoForm);

            $textoNovo = str_replace($dataContract['antes'], $dataContract['depois'], $findContrato->texto);

            $listContratos->object_id = $this->id;
            $listContratos->object_type = "Investiment";
            $listContratos->usuario_id = 0;
            $listContratos->contract = $textoNovo;
            $listContratos->contract_id = $findContrato->id;
            if(!$x_visualizar){
                $listContratos->save();
            }
            
        }else{
            $textoNovo = $dados->contract;
        }

        $pdf = Yii::$app->pdf;
        $pdf->filename = ($destination == Pdf::DEST_DOWNLOAD)? Yii::t('app', 'Contract the investiment') . ' - TBest.pdf' : 'investimento/contrato-investimento-tbest.pdf';
        $pdf->content = $textoNovo;
        $pdf->destination = $destination;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left">
                    <img width="25px" src="https://sistema.tbest.com.br/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];

        return $pdf->render();
    }

    public function sendContratoInvestimento($linkfatura)
    {
        $html = "<h4 style=\"text-align: center;\">Contrato de Investimento</h4>";
        $html .= "<h5 style=\"text-align: center;\" >Em anexo vai encontrar o contrato de investimento.</h5>";
        $html .= "<h5 style=\"text-align: center;\" >Aqui você encontra o link para a fatura para pagamento.</h5>";
        $html .= "<h5 style=\"text-align: center;\"><a href=\"".$linkfatura."\">Clique Aqui.</a></h5>";
        $person = $this->consumer->legalPerson;
        $mailer = Yii::$app->mailer->compose(
            'layouts/html',
            [
                'content' => $html,
            ]
        )
        ->setFrom(getenv('MAILER_FROM'))
        ->setTo($person->email)
        ->setSubject(Yii::t('app/mail', 'Contract Investiment'));
        if(file_exists('investimento/contrato-investimento-tbest.pdf'))
            $mailer->attach('investimento/contrato-investimento-tbest.pdf');
        
        $mailer->send();

        if(file_exists('investimento/contrato-investimento-tbest.pdf'))
            unlink('investimento/contrato-investimento-tbest.pdf');

        return true;
    }

    public function validarContratoInvestimento($consumer_id){
        $investimento = new Investimento();
        $investimento = $investimento->find()->Where(['consumer_id'=>$consumer_id])->one();
        if($investimento && ListContracts::find()->Where(['object_id'=>$investimento->id])->andWhere(['object_type'=>'Investiment'])->one()){
            return true;
        }
        return false;
    }
}