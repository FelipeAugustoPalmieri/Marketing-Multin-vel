<?php
namespace app\models\receitaws;

use Yii;
use RestClient;

class ConsultaCnpj
{
    public $api;
    public $msg;
    public $telefone;

    function __construct() {
        $this->api = new RestClient([
            'base_url'=> getenv('URL_RECEITAWS'),
            'headers' => []
        ]);
    }

    public function ConsultaCnpj($cnpj){
        return $this->api->get("cnpj/".$cnpj);
    }
}