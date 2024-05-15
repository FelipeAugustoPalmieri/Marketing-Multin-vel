<?php

namespace app\models;

use Yii;

class Plane extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'planes';
    }

    public function rules()
    {
        return [
            [['name_plane', 'multiplier', 'goal_points'], 'required'],
            [['multiplier', 'goal_points', 'value', 'bonus_percentage'], 'number'],
            [['name_plane', 'pay_plane'], 'string', 'max' => 255],
            [['name_plane'], 'unique'],
            [['value', 'bonus_percentage'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_plane' => Yii::t('app', 'Name Plane'),
            'multiplier' => Yii::t('app', 'Multiplier'),
            'goal_points' => Yii::t('app', 'Goal Points'),
            'value' => Yii::t('app', 'Value'),
            'bonus_percentage' => Yii::t('app', 'Bonus Percentage'),
            'pay_plane' => Yii::t('app', 'ID Payment'),
        ];
    }

    public function calculateProfitInvestiment($parcelas, $valorInvestimento = 0, $porcentagem = 0, $calculaInvestimento = true)
    {
        if($valorInvestimento <= 0){
            $valorInvestimento = Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO);
        }

        if($porcentagem <= 0){
            $porcentagem = Configuration::getConfigurationValue(Configuration::ID_PERCENTUAL_PARCELAS);
        }

        $porcentagemTotal = $porcentagem * intval($parcelas);
        $valorTotalJuros = ($this->value * number_format(($porcentagemTotal/100), 4, '.', ''));
        $valorTotalJuros = $valorTotalJuros + $this->value;
        $valorParcela = ($valorTotalJuros / intval($parcelas)) + (($calculaInvestimento)? $valorInvestimento : 0);

        return $valorParcela;
    }

    public function calculateValueTotal($parcelas, $valorInvestimento = 0, $porcentagem = 0){
        if($valorInvestimento <= 0){
            $valorInvestimento = Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO);
        }

        if($porcentagem <= 0){
            $porcentagem = Configuration::getConfigurationValue(Configuration::ID_PERCENTUAL_PARCELAS);
        }

        $porcentagemTotal = $porcentagem * intval($parcelas);
        $valorTotalJuros = ($this->value * number_format(($porcentagemTotal/100), 4, '.', ''));
        $valorTotalJuros = $valorTotalJuros + $this->value;

        return $valorTotalJuros;
    }

    public function getParcelas(){
        $parcelaMaximo = Configuration::getConfigurationValue(Configuration::ID_QUANTIDADE_MAXIMO_PARCELAS);
        $porcentagem = Configuration::getConfigurationValue(Configuration::ID_PERCENTUAL_PARCELAS);

        $retornoParcela = [];
        for($i = 0; $i <= $parcelaMaximo; $i++){
            switch($i){
                case(0):
                    $retornoParcela[] = array(
                        'parcela' => str_pad($i, 2, '0', STR_PAD_LEFT),
                        'valor' => Yii::$app->formatter->asCurrency($this->value)
                    );
                    break;
                case(1):
                    $valorTotalJuros = ($this->value * number_format(($porcentagem/100), 4, '.', ''));
                    $valorTotalJuros = $valorTotalJuros + $this->value;
                    
                    $retornoParcela[] = array(
                        'parcela' => str_pad($i, 2, '0', STR_PAD_LEFT),
                        'valor' => '30 dias '.Yii::$app->formatter->asCurrency($valorTotalJuros)
                    );
                    break;
                default:
                    $porcentagemTotal = $porcentagem*$i;
                    $valorTotalJuros = ($this->value * number_format(($porcentagemTotal/100), 4, '.', ''));
                    $valorTotalJuros = $valorTotalJuros + $this->value;
                    $valorParcela = $valorTotalJuros / $i;

                    $retornoParcela[] = array(
                        'parcela' => str_pad($i, 2, '0', STR_PAD_LEFT),
                        'valor' => Yii::$app->formatter->asCurrency($valorParcela)
                    );
                    break;
                
            }
        }
        
        if($this->id == 2){
            $retornoParcela[] = array(
                'parcela' => "---Parcelas Com Investimento---",
                'valor' => ""
            );
            $parcelasTituloInvestimento = Configuration::getConfigurationValue(Configuration::CONFIGURACAO_PARCELA_TITULO_INVESTIMENTO);
            $valorInvestimento = Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO);
            if($parcelasTituloInvestimento != "" &&  strlen($parcelasTituloInvestimento) > 0){
                $listParcelas = explode(",", $parcelasTituloInvestimento);
                foreach($listParcelas as $parcelas){
                    $nrparcelas = (preg_replace("/[^0-9]/", "", $parcelas) * 12);
                    $retornoParcela[] = array(
                        'parcela' => $parcelas,
                        'valor' => Yii::$app->formatter->asCurrency($this->calculateProfitInvestiment($nrparcelas, $valorInvestimento, $porcentagem))
                    );
                }
            }
        }

        return $retornoParcela;
    }

    public function calculateProfitValue(Plane $sponsorPlane)
    {
       return round((($this->value * $sponsorPlane->bonus_percentage) / 100), 2);
    }
}