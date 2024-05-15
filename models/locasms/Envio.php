<?php
namespace app\models\locasms;

use Yii;
use RestClient;

class Envio
{
    public $api;
    public $msg;
    public $telefone;

    function __construct() {
        $this->api = new RestClient([
            'base_url'=> getenv('URL_APILOCASMS'),
            'headers' => []
        ]);
    }

    public function envioSms(){
        return $this->api->get("", [
            "action" => "sendsms",
            "lgn"=>"49999403885",
            'pwd'=>'823533',
            'msg'=>'Teste de Envio de Sms pelo sistema LocaSms, Enviado pelo Sistema Tbest.',
            'numbers'=>$this->telefone
        ]);
    }
}