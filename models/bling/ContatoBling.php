<?php
namespace app\models\bling;

use Yii;

class ContatoBling
{
    public $url;
    public $xml;

    function __construct() {
        $this->url = getenv('URL_APIBLING');
    }

    public function generateXml($consumer){
        $xml = "";
        $xml .= "<contato>";
        $xml .= "<nome>".$consumer->legalPerson->name."</nome>";
        $xml .= "<tipoPessoa>".(strlen(preg_replace("/[^0-9]/", "", $consumer->legalPerson->person->cpf)) > 11 ? "J" : "F")."</tipoPessoa>";
        $xml .= "<contribuinte>9</contribuinte>";
        $xml .= "<cpf_cnpj>".preg_replace("/[^0-9]/", "", $consumer->legalPerson->person->cpf)."</cpf_cnpj>";
        $xml .= "<endereco>".$consumer->legalPerson->address."</endereco>";
        $xml .= "<numero>".preg_replace("/[^0-9]/", "", $consumer->legalPerson->address)."</numero>";
        $xml .= "<complemento>".$consumer->legalPerson->address."</complemento>";
        $xml .= "<bairro>".$consumer->legalPerson->district."</bairro>";
        $xml .= "<cep>".preg_replace("/[^0-9]/", "", $consumer->legalPerson->zip_code)."</cep>";
        $xml .= "<cidade>".$consumer->legalPerson->city->name."</cidade>";
        $xml .= "<uf>".$consumer->legalPerson->city->state->abbreviation."</uf>";
        $xml .= "<celular>".preg_replace("/[^0-9]/", "", $consumer->legalPerson->phoneNumber)."</celular>";
        $xml .= "<email>".$consumer->legalPerson->email."</email>";
        $xml .= "<codigo>".$consumer->identifier."</codigo>";
        $xml .= "</contato>";
        $this->xml = $xml;
        return $this->xml;
    }

    public function prepararCliente($cliente){
        if($cliente->id_bling <= 0){
            $this->cpfCnpj = preg_replace("/[^0-9]/", "", $cliente->legalPerson->person->cpf);
            $dadosRetorno = json_decode($this->FiltroBuscarCliente());
            if(!isset($dadosRetorno->retorno->erros) && isset($dadosRetorno->retorno->contatos)){
                $cliente->id_bling = $dadosRetorno->retorno->contatos[0]->contato->id;
                $cliente->updateIdBling();
            }else{
                $xml = $this->generateXml($cliente);
                $retorno = json_decode($this->CadastrarCliente());
                $cliente->id_bling = $retorno->retorno->contatos->contato->id;
                $cliente->updateIdBling();
            }
        }
    }

    public function CadastrarCliente(){
        $retorno = $this->PostContato('contato/', [ "apikey" => getenv('TOKEN_APIBLING'), "xml" => $this->xml ]);
        return $retorno;
    }

    public function FiltroBuscarCliente(){
        $retorno = $this->GetContato('contato/');
        return $retorno;
    }

    function PostContato($method, $data){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $this->url . $method . 'json');
        curl_setopt($curl_handle, CURLOPT_POST, count($data));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }

    public function GetContato($method){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $this->url . $method . $this->cpfCnpj . '/json&apikey=' . getenv('TOKEN_APIBLING'));
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
}

?>