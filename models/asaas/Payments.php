<?php
namespace app\models\asaas;

use Yii;
use RestClient;

class Payments
{
    public $customer;
    public $billingType = "UNDEFINED";
    public $dueDate;
    public $value;
    public $description;
    public $externalReference;
    public $discountValue = 0;
    public $dueDateLimitDays = 0;
    public $interestValue = 0;
    public $fineValue = 0;
    public $postalService = false;
    public $installmentCount = 1;
    public $installmentValue;


    function __construct() {
        $this->api = new RestClient([
            'base_url'=> getenv('URL_APIASAAS'),
            'headers' => [
                'Accept' => 'application/json',
                'access_token' => getenv('TOKEN_APIASAAS'),
            ]
        ]);
    }

    public function CadastrarPagamento(){
        return $this->api->post('payments', [
            "customer" => $this->customer,
            "billingType" => $this->billingType,
            "dueDate" => date('Y-m-d', strtotime('+3 days')),
            "value" => $this->value,
            "description" => $this->description,
            "externalReference" => $this->externalReference,
            "installmentCount" => $this->installmentCount,
            "installmentValue" => $this->value/$this->installmentCount,
            "discount" => [
                "value" => $this->discountValue,
                "dueDateLimitDays" => $this->dueDateLimitDays
            ],
            "fine" => [
                "value" => $this->fineValue,
            ],
            "interest" => [
                "value" => $this->interestValue,
            ],
            "postalService" => $this->postalService
        ]);
    }
}