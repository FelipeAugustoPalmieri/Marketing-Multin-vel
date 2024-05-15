<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

use Yii;
use mPDF;
use kartik\mpdf\Pdf;

class Contratos extends \yii\db\ActiveRecord
{
    private $tipoContratos = array(
        1=>"Contratos Investimentos",
        2=>"Contratos Consumidores",
        3=>"Contratos Convênios",
    );

    public $tituloTermos;

    public static function tableName()
    {
        return 'contratos';
    }

    public function rules()
    {
        return [
            [['titulo', 'texto', 'flag_local', 'flag_cancel'], 'required'],
            [['titulo'], 'string', 'max' => 150]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'titulo' => Yii::t('app', 'titulo'),
            'texto' => Yii::t('app', 'texto'),
            'flag_local' => Yii::t('app', 'local'),
            'flag_cancel' => Yii::t('app', 'contract cancel'),
            'created_at' => Yii::t('app', 'created_at'),
            'updated_at' => Yii::t('app', 'updated_at'),
        ];
    }

    public function getFlagLocal($flag_local){
        return !isset($this->tipoContratos[$flag_local])? Yii::t('app', 'Não Informado') : $this->tipoContratos[$flag_local];
    }

    public function buscarTermosCadastro($render = true){
        $findContrato = $this->find()->Where(['flag_local'=>2])->andWhere(['flag_cancel'=>0])->orderBy(['created_at'=>SORT_DESC])->one();
        return $this->gerarPdf($findContrato->texto, $render);
    }

    private function gerarPdf($texto, $render){
        $pdf = Yii::$app->pdf;
        $pdf->filename = ($this->tituloTermos)? $this->tituloTermos : Yii::t('app', 'Regulamento da Tbest para Cadastro Consumidor') . ' - TBest.pdf';
        $pdf->content = $texto;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left">
                    <img width="25px" src="https://sistema.tbest.com.br/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];

        return (($render) ? $pdf->render() : $pdf);
    }

    public function InvestimentoContract(){
        return array(
            '@nomeconsumidor', 
            '@cpfconsumidor', 
            '@rgconsumidor',
            '@cepconsumidor',
            '@profissaoconsumidor',
            '@ruaconsumidor',
            '@numeroconsumidor',
            '@bairroconsumidor',
            '@cidadeconsumidor',
            '@estadoconsumidor',
            '@valorinvestimento',
            '@tempoanosinvestimento',
            '@tempoanosextensoinvestimento',
            '@valorextensoinvestimento',
            '@datacreate',
            '@nomepresidente',
            '@cpfpresidente'
        );
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
}