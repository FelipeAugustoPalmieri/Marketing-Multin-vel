<?php
namespace app\models\asaas;

use Yii;
use RestClient;

class Costumers
{
    public $name;
    public $email;
    public $phone;
    public $mobilePhone;
    public $cpfCnpj;
    public $postalCode;
    public $address;
    public $addressNumber;
    public $complement;
    public $province;
    public $externalReference;
    public $notificationDisabled = false;
    public $additionalEmails = "";
    public $municipalInscription = "";
    public $stateInscription = "";
    public $api;

    function __construct() {
        $this->api = new RestClient([
            'base_url'=> getenv('URL_APIASAAS'),
            'headers' => [
                'Accept' => 'application/json',
                'access_token' => getenv('TOKEN_APIASAAS'),
            ]
        ]);
    }

    public function CadastrarCliente(){
        return $this->api->post('customers', [
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "mobilePhone" => $this->mobilePhone,
            "cpfCnpj" => $this->cpfCnpj,
            "postalCode" => $this->postalCode,
            "address" => $this->address,
            "addressNumber" => $this->addressNumber,
            "complement" => $this->complement,
            "province" => $this->province,
            "externalReference" => $this->externalReference,
            "notificationDisabled" => $this->notificationDisabled,
            "additionalEmails" => $this->additionalEmails,
            "municipalInscription" => $this->municipalInscription,
            "stateInscription" => $this->stateInscription
        ]);
    }

    public function preparaCliente($consumer){
        $verifica = $this->FiltroBuscarCliente();
        $dadosRetorno = json_decode($verifica->response);
        
        if($dadosRetorno->totalCount > 0){
            $dadosRetorno = $dadosRetorno->data[0];
            $consumer->id_asaas = $dadosRetorno->id;
            $consumer->updateIdAsaas();
        }else{
            $result = $this->CadastrarCliente();
            $dadosRetorno = json_decode($result->response);
            $consumer->id_asaas = $dadosRetorno->id;
            $consumer->updateIdAsaas();
        }
    }

    public function FiltroBuscarCliente(){
        return $this->api->get('customers',[
            "cpfCnpj" => $this->cpfCnpj,
        ]);
    }
}