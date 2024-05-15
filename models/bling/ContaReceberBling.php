<?php
namespace app\models\bling;

use Yii;
use app\models\Configuration;

class ContaReceberBling
{
    public $url;
    public $xml;

    function __construct() {
        $this->url = getenv('URL_APIBLING');
    }

    public function generateXml($consumer, $contareceber, $historico, $plane){
        $parcelaMaximo = Configuration::getConfigurationValue(Configuration::ID_QUANTIDADE_MAXIMO_PARCELAS);
        $xml = "";
        $xml .= "<contareceber>";
        $xml .= "<dataEmissao>".Yii::$app->formatter->asDatetime(new \DateTime(), 'php:d/m/Y')."</dataEmissao>";
        $xml .= "<vencimentoOriginal>".date('d/m/Y', strtotime('+3 days'))."</vencimentoOriginal>";
        $xml .= "<nroDocumento>".$contareceber->nr_documento."</nroDocumento>";
        $xml .= "<valor>". $plane->calculateProfitInvestiment($contareceber->nr_parcelas, 0, 0, (($contareceber->nr_parcelas > $parcelaMaximo)? true : false)) ."</valor>";
        $xml .= "<historico>".$historico."</historico>";
        $xml .= "<categoria>Titulo ".$plane->name_plane."</categoria>";
        $xml .= "<idFormaPagamento>705800</idFormaPagamento>";
        $xml .= "<portador>".$consumer->legalPerson->name."</portador>";
        //$xml .= "<vendedor>".$consumer->sponsorConsumer->legalPerson->name."</vendedor>";
        $xml .= "<ocorrencia>";
        $xml .= "<ocorrenciaTipo>".(($contareceber->nr_parcelas <= 1 || $contareceber->nr_parcelas > $parcelaMaximo)? "U" : "P" )."</ocorrenciaTipo>";
        $xml .= "<diaVencimento>".date("d")."</diaVencimento>";
        $xml .= "<nroParcelas>".(($contareceber->nr_parcelas > $parcelaMaximo)? "1" : $contareceber->nr_parcelas)."</nroParcelas>";
        $xml .= "</ocorrencia>";
        $xml .= "<cliente>";
        $xml .= "<nome>".$consumer->legalPerson->name."</nome>";
        $xml .= "<id>".$consumer->id_bling."</id>";
        $xml .= "</cliente>";
        $xml .= "</contareceber>";
        $this->xml = $xml;
    }

    public function generateXmlInvestimento($consumer, $contareceber, $historico, $investimento){
        $parcelaMaximo = Configuration::getConfigurationValue(Configuration::ID_QUANTIDADE_MAXIMO_PARCELAS);
        $xml = "";
        $xml .= "<contareceber>";
        $xml .= "<dataEmissao>".Yii::$app->formatter->asDatetime(new \DateTime(), 'php:d/m/Y')."</dataEmissao>";
        $xml .= "<vencimentoOriginal>".date('d/m/Y', strtotime('+3 days'))."</vencimentoOriginal>";
        $xml .= "<nroDocumento>".$contareceber->nr_documento."</nroDocumento>";
        $xml .= "<valor>". $investimento->valor ."</valor>";
        $xml .= "<historico>".$historico."</historico>";
        $xml .= "<categoria>Investimento - ".(intval($investimento->prazo)/12)." Anos </categoria>";
        $xml .= "<idFormaPagamento>705800</idFormaPagamento>";
        $xml .= "<portador>".$consumer->legalPerson->name."</portador>";
        //$xml .= "<vendedor>".$consumer->sponsorConsumer->legalPerson->name."</vendedor>";
        $xml .= "<ocorrencia>";
        $xml .= "<ocorrenciaTipo>U</ocorrenciaTipo>";
        $xml .= "<diaVencimento>".date("d")."</diaVencimento>";
        $xml .= "<nroParcelas>1</nroParcelas>";
        $xml .= "</ocorrencia>";
        $xml .= "<cliente>";
        $xml .= "<nome>".$consumer->legalPerson->name."</nome>";
        $xml .= "<id>".$consumer->id_bling."</id>";
        $xml .= "</cliente>";
        $xml .= "</contareceber>";
        $this->xml = $xml;
    }

    public function CadastrarContaReceber(){
        return $this->PostContaReceber('contareceber/', [ "apikey" => getenv('TOKEN_APIBLING'), "xml" => $this->xml ]);
    }

    public function PostContaReceber($method, $data){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $this->url . $method . 'json');
        curl_setopt($curl_handle, CURLOPT_POST, count($data));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
}